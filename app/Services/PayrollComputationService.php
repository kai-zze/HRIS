<?php

namespace App\Services;

use App\Enums\EmploymentStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\PagibigSetting;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\PhilhealthSetting;
use App\Models\SalaryStructure;
use App\Models\SssContributionBracket;
use App\Models\WithholdingTaxBracket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollComputationService
{
    /**
     * Compute and generate/update payroll items for an entire payroll run.
     */
    public function computePayrollRun(PayrollRun $run): void
    {
        DB::transaction(function () use ($run) {
            $employees = Employee::withoutGlobalScopes()
                ->where('entity_id', $run->entity_id)
                ->whereNotIn('employment_status', [EmploymentStatus::Resigned, EmploymentStatus::Terminated])
                ->get();

            $totalGross = 0.00;
            $totalDeductions = 0.00;
            $totalNet = 0.00;
            $employeeCount = 0;

            foreach ($employees as $employee) {
                $item = $this->computeEmployeePayrollItem($run, $employee);
                if ($item) {
                    $totalGross += $item->gross_pay;
                    $totalDeductions += $item->total_deductions;
                    $totalNet += $item->net_pay;
                    $employeeCount++;
                }
            }

            $run->update([
                'total_gross' => $totalGross,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet,
                'total_employees' => $employeeCount,
            ]);
        });
    }

    /**
     * Compute individual payroll breakdown for an employee in a given run.
     */
    public function computeEmployeePayrollItem(PayrollRun $run, Employee $employee): PayrollItem
    {
        $structure = $employee->salaryStructure ?? SalaryStructure::firstOrCreate(
            ['employee_id' => $employee->id],
            [
                'basic_salary' => $employee->basic_salary ?: 25000.00,
                'pay_frequency' => 'semi-monthly',
                'payment_method' => 'bank_transfer',
                'bank_name' => $employee->bank_name ?: 'BDO',
                'bank_account_number' => $employee->bank_account_number ?: '001234567890',
                'is_minimum_wage_earner' => (($employee->basic_salary ?: 25000.00) <= 15000.00),
                'daily_rate' => round((($employee->basic_salary ?: 25000.00) * 12) / 261, 2),
                'hourly_rate' => round(((($employee->basic_salary ?: 25000.00) * 12) / 261) / 8, 2),
                'effective_date' => '2026-01-01',
                'is_active' => true,
            ]
        );

        $basicSalary = (float) $structure->basic_salary;
        $dailyRate = (float) ($structure->daily_rate ?: round(($basicSalary * 12) / 261, 2));
        $hourlyRate = (float) ($structure->hourly_rate ?: round($dailyRate / 8, 2));

        // Semi-monthly basic pay
        $basicPayEarned = round($basicSalary / 2, 2);

        // 1. Pull DTR Punches & Computations between cutoff_start and cutoff_end
        $dtrs = DailyTimeRecord::withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->whereBetween('record_date', [$run->cutoff_start, $run->cutoff_end])
            ->get();

        $absentDays = 0.00;
        $lateMinutes = 0;
        $undertimeMinutes = 0;
        $regularOtHours = 0.00;
        $restDayOtHours = 0.00;
        $holidayOtHours = 0.00;

        foreach ($dtrs as $dtr) {
            if ($dtr->status === 'absent') {
                $absentDays += 1.00;
            } elseif ($dtr->status === 'half_day') {
                $absentDays += 0.50;
            }

            $lateMinutes += (int) $dtr->late_minutes;
            $undertimeMinutes += (int) $dtr->undertime_minutes;
            $regularOtHours += (float) $dtr->overtime_hours;
        }

        $absentDeduction = round($absentDays * $dailyRate, 2);
        $lateDeduction = round($lateMinutes * ($hourlyRate / 60), 2);
        $undertimeDeduction = round($undertimeMinutes * ($hourlyRate / 60), 2);

        // OT Calculations under Philippine Labor Code
        $regularOtPay = round($regularOtHours * $hourlyRate * 1.25, 2);
        $restDayOtPay = round($restDayOtHours * $hourlyRate * 1.30, 2);
        $holidayOtPay = round($holidayOtHours * $hourlyRate * 2.00, 2);

        // 2. Allowances
        $taxableAllowances = 0.00;
        $nonTaxableAllowances = 0.00;
        $allowancesBreakdown = [];

        foreach ($employee->salaryAllowances()->where('is_active', true)->get() as $allowance) {
            $amount = (float) $allowance->amount;
            if ($allowance->frequency === 'monthly') {
                $amount = round($amount / 2, 2);
            }

            if ($allowance->is_taxable) {
                $taxableAllowances += $amount;
            } else {
                $nonTaxableAllowances += $amount;
            }

            $allowancesBreakdown[] = [
                'name' => $allowance->name,
                'amount' => $amount,
                'taxable' => $allowance->is_taxable,
            ];
        }

        // 3. Gross Pay
        $adjustedBasic = max(0.00, $basicPayEarned - $absentDeduction - $lateDeduction - $undertimeDeduction);
        $grossPay = round(
            $adjustedBasic +
            $regularOtPay +
            $restDayOtPay +
            $holidayOtPay +
            $taxableAllowances +
            $nonTaxableAllowances,
            2
        );

        // 4. Statutory Deductions (Split 50% for Semi-Monthly Cutoff)
        $statutory = $this->calculateStatutoryContributions($basicSalary);

        $sssEe = round($statutory['sss_ee'] / 2, 2);
        $sssEr = round($statutory['sss_er'] / 2, 2);
        $sssEc = round($statutory['sss_ec'] / 2, 2);
        $sssWispEe = round($statutory['sss_wisp_ee'] / 2, 2);
        $sssWispEr = round($statutory['sss_wisp_er'] / 2, 2);

        $philhealthEe = round($statutory['philhealth_ee'] / 2, 2);
        $philhealthEr = round($statutory['philhealth_er'] / 2, 2);

        $pagibigEe = round($statutory['pagibig_ee'] / 2, 2);
        $pagibigEr = round($statutory['pagibig_er'] / 2, 2);

        $totalStatutoryEe = $sssEe + $sssWispEe + $philhealthEe + $pagibigEe;

        // 5. Withholding Tax (TRAIN Law)
        $withholdingTax = 0.00;
        $taxableIncome = 0.00;

        if (! $structure->is_minimum_wage_earner) {
            $taxableIncome = max(0.00, $grossPay - $nonTaxableAllowances - $totalStatutoryEe);
            $withholdingTax = $this->calculateWithholdingTax($taxableIncome, 'semi-monthly');
        }

        // 6. Loans & Other Deductions
        $loanDeductions = 0.00;
        $loanBreakdown = [];

        $activeLoans = EmployeeLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('balance_remaining', '>', 0)
            ->get();

        foreach ($activeLoans as $loan) {
            $deduction = min((float) $loan->amortization_per_cutoff, (float) $loan->balance_remaining);
            $loanDeductions += $deduction;

            $loanBreakdown[] = [
                'loan_id' => $loan->id,
                'loan_type' => $loan->loan_type,
                'amount' => $deduction,
                'remaining' => max(0.00, (float) $loan->balance_remaining - $deduction),
            ];

            // If the payroll run is locked or approved, update loan remaining balance
            if (in_array($run->status, ['approved', 'locked'])) {
                $newBalance = max(0.00, (float) $loan->balance_remaining - $deduction);
                $loan->update([
                    'balance_remaining' => $newBalance,
                    'status' => ($newBalance <= 0) ? 'paid' : 'active',
                ]);
            }
        }

        $otherDeductions = 0.00;
        $deductionsBreakdown = [];
        foreach ($employee->salaryDeductions()->where('is_active', true)->get() as $ded) {
            $amt = (float) $ded->amount;
            if ($ded->frequency === 'monthly') {
                $amt = round($amt / 2, 2);
            }
            $otherDeductions += $amt;
            $deductionsBreakdown[] = [
                'name' => $ded->name,
                'amount' => $amt,
            ];
        }

        // 7. Totals & Net Pay
        $totalDeductions = round(
            $totalStatutoryEe +
            $withholdingTax +
            $loanDeductions +
            $otherDeductions,
            2
        );

        $netPay = max(0.00, round($grossPay - $totalDeductions, 2));

        return PayrollItem::updateOrCreate(
            [
                'payroll_run_id' => $run->id,
                'employee_id' => $employee->id,
            ],
            [
                'basic_salary' => $basicSalary,
                'basic_pay_earned' => $basicPayEarned,
                'daily_rate' => $dailyRate,
                'hourly_rate' => $hourlyRate,
                'absent_days' => $absentDays,
                'absent_deduction' => $absentDeduction,
                'late_minutes' => $lateMinutes,
                'late_deduction' => $lateDeduction,
                'undertime_minutes' => $undertimeMinutes,
                'undertime_deduction' => $undertimeDeduction,
                'regular_ot_hours' => $regularOtHours,
                'regular_ot_pay' => $regularOtPay,
                'rest_day_ot_hours' => $restDayOtHours,
                'rest_day_ot_pay' => $restDayOtPay,
                'holiday_ot_hours' => $holidayOtHours,
                'holiday_ot_pay' => $holidayOtPay,
                'taxable_allowances' => $taxableAllowances,
                'non_taxable_allowances' => $nonTaxableAllowances,
                'gross_pay' => $grossPay,
                'sss_ee' => $sssEe,
                'sss_er' => $sssEr,
                'sss_ec' => $sssEc,
                'sss_wisp_ee' => $sssWispEe,
                'sss_wisp_er' => $sssWispEr,
                'philhealth_ee' => $philhealthEe,
                'philhealth_er' => $philhealthEr,
                'pagibig_ee' => $pagibigEe,
                'pagibig_er' => $pagibigEr,
                'taxable_income' => $taxableIncome,
                'withholding_tax' => $withholdingTax,
                'loan_deductions' => $loanDeductions,
                'other_deductions' => $otherDeductions,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'payment_method' => $structure->payment_method ?: 'bank_transfer',
                'bank_name' => $structure->bank_name,
                'bank_account_number' => $structure->bank_account_number,
                'metadata' => [
                    'allowances' => $allowancesBreakdown,
                    'deductions' => $deductionsBreakdown,
                    'loans' => $loanBreakdown,
                ],
            ]
        );
    }

    /**
     * Compute full monthly statutory contributions based on Philippine database tables.
     */
    public function calculateStatutoryContributions(float $monthlyBasicSalary): array
    {
        // 1. SSS Calculation from database bracket
        $sssBracket = SssContributionBracket::where('is_active', true)
            ->where('range_min', '<=', $monthlyBasicSalary)
            ->where(function ($q) use ($monthlyBasicSalary) {
                $q->whereNull('range_max')
                    ->orWhere('range_max', '>=', $monthlyBasicSalary);
            })
            ->first();

        if (! $sssBracket) {
            // Default to highest or lowest bracket if outside range
            $sssBracket = ($monthlyBasicSalary < 4000)
                ? SssContributionBracket::orderBy('range_min')->first()
                : SssContributionBracket::orderByDesc('monthly_salary_credit')->first();
        }

        $sssEe = (float) ($sssBracket?->regular_employee_share ?? 0.00);
        $sssEr = (float) ($sssBracket?->regular_employer_share ?? 0.00);
        $sssEc = (float) ($sssBracket?->ec_employer_share ?? 10.00);
        $sssWispEe = (float) ($sssBracket?->wisp_employee_share ?? 0.00);
        $sssWispEr = (float) ($sssBracket?->wisp_employer_share ?? 0.00);

        // 2. PhilHealth Calculation from settings
        $phSetting = PhilhealthSetting::where('is_active', true)->first();
        $phRate = (float) ($phSetting?->premium_rate_percent ?? 5.00) / 100;
        $phFloor = (float) ($phSetting?->income_floor ?? 10000.00);
        $phCeiling = (float) ($phSetting?->income_ceiling ?? 100000.00);
        $phEePercent = (float) ($phSetting?->employee_share_percent ?? 50.00) / 100;
        $phErPercent = (float) ($phSetting?->employer_share_percent ?? 50.00) / 100;

        $phSalaryBase = min(max($monthlyBasicSalary, $phFloor), $phCeiling);
        $totalPhPremium = round($phSalaryBase * $phRate, 2);
        $philhealthEe = round($totalPhPremium * $phEePercent, 2);
        $philhealthEr = round($totalPhPremium * $phErPercent, 2);

        // 3. Pag-IBIG Calculation from settings
        $pagibigSetting = PagibigSetting::where('is_active', true)->first();
        $pagibigEe = (float) ($pagibigSetting?->mandatory_employee_share ?? 200.00);
        $pagibigEr = (float) ($pagibigSetting?->mandatory_employer_share ?? 200.00);

        return [
            'sss_ee' => $sssEe,
            'sss_er' => $sssEr,
            'sss_ec' => $sssEc,
            'sss_wisp_ee' => $sssWispEe,
            'sss_wisp_er' => $sssWispEr,
            'philhealth_ee' => $philhealthEe,
            'philhealth_er' => $philhealthEr,
            'pagibig_ee' => $pagibigEe,
            'pagibig_er' => $pagibigEr,
        ];
    }

    /**
     * Compute withholding tax under TRAIN Law semi-monthly table.
     */
    public function calculateWithholdingTax(float $taxableIncome, string $frequency = 'semi-monthly'): float
    {
        if ($taxableIncome <= 0) {
            return 0.00;
        }

        $bracket = WithholdingTaxBracket::where('is_active', true)
            ->where('pay_frequency', $frequency)
            ->where('range_min', '<=', $taxableIncome)
            ->where(function ($q) use ($taxableIncome) {
                $q->whereNull('range_max')
                    ->orWhere('range_max', '>=', $taxableIncome);
            })
            ->first();

        if (! $bracket) {
            return 0.00;
        }

        $baseTax = (float) $bracket->base_tax;
        $excessRate = (float) $bracket->excess_rate_percent / 100;
        $excess = max(0.00, $taxableIncome - (float) $bracket->range_min);

        return round($baseTax + ($excess * $excessRate), 2);
    }
}
