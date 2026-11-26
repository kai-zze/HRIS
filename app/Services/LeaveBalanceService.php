<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Exception;

class LeaveBalanceService
{
    /**
     * Allocate default credits to an employee for a specific year.
     */
    public function allocateYearlyCredits(Employee $employee, int $year): void
    {
        $leaveTypes = LeaveType::where('is_active', true)
            ->where(function ($q) use ($employee) {
                $q->whereNull('entity_id')->orWhere('entity_id', $employee->entity_id);
            })->get();

        foreach ($leaveTypes as $type) {
            LeaveCredit::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $type->id,
                    'year' => $year,
                ],
                [
                    'allocated_days' => $type->default_credit_days,
                    'used_days' => 0,
                    'pending_days' => 0,
                    'remaining_days' => $type->default_credit_days,
                ]
            );
        }
    }

    /**
     * Get available balance for an employee.
     */
    public function getAvailableBalance(Employee $employee, int $leaveTypeId, int $year): float
    {
        $credit = LeaveCredit::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();

        if (! $credit) {
            return 0.0;
        }

        $remaining = (float) max(0, $credit->allocated_days - $credit->used_days);

        return (float) max(0, $remaining - $credit->pending_days);
    }

    /**
     * Check if employee has sufficient balance.
     */
    public function hasSufficientBalance(Employee $employee, int $leaveTypeId, float $days, int $year): bool
    {
        $credit = LeaveCredit::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();

        if (! $credit) {
            return false;
        }

        return ($credit->remaining_days - $credit->pending_days) >= $days;
    }

    /**
     * Deduct from balance upon approval.
     */
    public function deductBalance(LeaveRequest $request): void
    {
        $year = (int) $request->start_date->format('Y');

        $credit = LeaveCredit::firstOrCreate(
            [
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $year,
            ],
            [
                'allocated_days' => 15,
                'used_days' => 0,
                'pending_days' => 0,
                'remaining_days' => 15,
            ]
        );

        $days = (float) $request->number_of_days;
        $credit->used_days += $days;
        $credit->pending_days = max(0, $credit->pending_days - $days);
        $credit->remaining_days = max(0, $credit->allocated_days - $credit->used_days);
        $credit->save();
    }
}
