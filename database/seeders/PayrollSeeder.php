<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Entity;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\SalaryAllowance;
use App\Models\SalaryDeduction;
use App\Models\SalaryStructure;
use App\Services\PayrollComputationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::withoutGlobalScopes()->get();

        foreach ($employees as $employee) {
            $basic = $employee->basic_salary ?: 25000.00;
            // 261 working days factor (standard 5-day work week in PH)
            $dailyRate = round(($basic * 12) / 261, 2);
            $hourlyRate = round($dailyRate / 8, 2);

            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'basic_salary' => $basic,
                    'pay_frequency' => 'semi-monthly',
                    'payment_method' => 'bank_transfer',
                    'bank_name' => $employee->bank_name ?: 'BDO',
                    'bank_account_number' => $employee->bank_account_number ?: '001234567890',
                    'is_minimum_wage_earner' => ($basic <= 15000.00),
                    'daily_rate' => $dailyRate,
                    'hourly_rate' => $hourlyRate,
                    'effective_date' => '2026-01-01',
                    'is_active' => true,
                ]
            );

            // Recurring Allowance: Transportation & Communication
            SalaryAllowance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'name' => 'Transportation Allowance',
                ],
                [
                    'amount' => 1500.00,
                    'frequency' => 'per_cutoff',
                    'is_taxable' => false,
                    'is_active' => true,
                ]
            );

            // Occasional sample loan for Employee #1 and #2
            if (in_array($employee->id, [1, 2])) {
                EmployeeLoan::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'loan_type' => 'SSS Salary Loan',
                    ],
                    [
                        'reference_number' => 'SSS-LN-2026-00' . $employee->id,
                        'principal_amount' => 24000.00,
                        'term_months' => 24,
                        'monthly_amortization' => 1000.00,
                        'amortization_per_cutoff' => 500.00,
                        'balance_remaining' => 18500.00,
                        'start_date' => '2025-06-01',
                        'end_date' => '2027-05-31',
                        'status' => 'active',
                        'notes' => 'Direct salary deduction via SSS billing',
                    ]
                );
            }
        }

        // Seed an approved Payroll Run for January 1-15, 2026 for the primary entity STC
        $entity = Entity::where('code', 'STC')->first() ?? Entity::first();
        if ($entity) {
            $cutoffStart = Carbon::parse('2026-01-01');
            $cutoffEnd = Carbon::parse('2026-01-15');
            $payoutDate = Carbon::parse('2026-01-20');

            $payrollRun = PayrollRun::updateOrCreate(
                [
                    'entity_id' => $entity->id,
                    'name' => "January 1-15, 2026 Regular Cutoff - {$entity->short_name}",
                ],
                [
                    'cutoff_start' => $cutoffStart,
                    'cutoff_end' => $cutoffEnd,
                    'payout_date' => $payoutDate,
                    'period_type' => 'first_half',
                    'year' => 2026,
                    'month' => 1,
                    'status' => 'approved',
                    'notes' => 'First semi-monthly payroll run generated and approved.',
                ]
            );

            $computationService = app(PayrollComputationService::class);
            $computationService->computePayrollRun($payrollRun);
        }
    }
}
