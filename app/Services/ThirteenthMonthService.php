<?php

namespace App\Services;

use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\ThirteenthMonthRecord;
use Illuminate\Support\Facades\DB;

class ThirteenthMonthService
{
    /**
     * Compute 13th month pay for all employees in an entity for a given year.
     * Formula: Total Basic Salary Earned in the Calendar Year / 12 (PD 851)
     * Tax Exemption: Up to ₱90,000 tax exempt under RA 10963 TRAIN Law.
     */
    public function computeEntityThirteenthMonth(int $entityId, int $year): array
    {
        return DB::transaction(function () use ($entityId, $year) {
            $employees = Employee::withoutGlobalScopes()
                ->where('entity_id', $entityId)
                ->whereNotIn('employment_status', [EmploymentStatus::Resigned, EmploymentStatus::Terminated])
                ->get();

            $records = [];

            foreach ($employees as $employee) {
                $record = $this->computeEmployeeThirteenthMonth($employee, $year);
                if ($record) {
                    $records[] = $record;
                }
            }

            return $records;
        });
    }

    /**
     * Compute 13th month pay for a single employee.
     */
    public function computeEmployeeThirteenthMonth(Employee $employee, int $year): ThirteenthMonthRecord
    {
        // 1. Sum up all basic_pay_earned from approved/locked payroll runs in this year
        $totalBasicEarned = (float) PayrollItem::where('employee_id', $employee->id)
            ->whereHas('payrollRun', function ($q) use ($year) {
                $q->where('year', $year)
                    ->whereIn('status', ['approved', 'locked']);
            })
            ->sum('basic_pay_earned');

        // If no payroll items exist yet (e.g. at start of year), estimate based on monthly salary and months worked
        $monthsWorked = 12.00;
        if ($totalBasicEarned <= 0) {
            $monthlyBasic = (float) ($employee->basic_salary ?: 25000.00);
            $hiredDate = $employee->date_hired;
            if ($hiredDate && $hiredDate->year == $year) {
                $monthsWorked = max(1.00, round(12 - $hiredDate->month + 1, 2));
            }
            $totalBasicEarned = round($monthlyBasic * $monthsWorked, 2);
        }

        $thirteenthMonthPay = round($totalBasicEarned / 12, 2);

        // Tax exemption check: ₱90,000 ceiling under TRAIN Law
        $taxExemptAmount = min($thirteenthMonthPay, 90000.00);
        $taxableAmount = max(0.00, round($thirteenthMonthPay - 90000.00, 2));

        return ThirteenthMonthRecord::updateOrCreate(
            [
                'entity_id' => $employee->entity_id,
                'employee_id' => $employee->id,
                'year' => $year,
            ],
            [
                'total_basic_pay_earned' => $totalBasicEarned,
                'months_worked' => $monthsWorked,
                'thirteenth_month_pay' => $thirteenthMonthPay,
                'tax_exempt_amount' => $taxExemptAmount,
                'taxable_amount' => $taxableAmount,
                'status' => 'pending',
            ]
        );
    }
}
