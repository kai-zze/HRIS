<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Compute and create or update DTR row based on time in/out and schedule.
     */
    public function computeAttendance(
        Employee $employee,
        string|Carbon $date,
        ?string $timeIn = null,
        ?string $timeOut = null,
        string $source = 'manual',
        ?string $remarks = null
    ): DailyTimeRecord {
        $recordDate = Carbon::parse($date)->startOfDay();

        // Check if day is holiday
        $holiday = Holiday::where('holiday_date', $recordDate->toDateString())
            ->where(function ($q) use ($employee) {
                $q->whereNull('entity_id')->orWhere('entity_id', $employee->entity_id);
            })->first();

        // Check approved leave
        $approvedLeave = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $recordDate)
            ->whereDate('end_date', '>=', $recordDate)
            ->first();

        // Get applicable schedule
        $schedule = $employee->workSchedule
            ?? WorkSchedule::where('entity_id', $employee->entity_id)->where('is_default', true)->first()
            ?? WorkSchedule::where('entity_id', $employee->entity_id)->first();

        $schedIn = $schedule ? Carbon::parse($recordDate->toDateString() . ' ' . $schedule->time_in) : Carbon::parse($recordDate->toDateString() . ' 08:00:00');
        $schedOut = $schedule ? Carbon::parse($recordDate->toDateString() . ' ' . $schedule->time_out) : Carbon::parse($recordDate->toDateString() . ' 17:00:00');
        $graceMinutes = $schedule ? $schedule->grace_period_minutes : 15;
        $breakMinutes = $schedule ? $schedule->break_minutes : 60;

        $lateMinutes = 0;
        $undertimeMinutes = 0;
        $regularHours = 0;
        $status = AttendanceStatus::Present;

        if ($approvedLeave) {
            $status = AttendanceStatus::OnLeave;
        } elseif (! $timeIn && ! $timeOut) {
            $status = $holiday ? AttendanceStatus::Holiday : AttendanceStatus::Absent;
        } else {
            $inTime = $timeIn ? Carbon::parse($recordDate->toDateString() . ' ' . $timeIn) : null;
            $outTime = $timeOut ? Carbon::parse($recordDate->toDateString() . ' ' . $timeOut) : null;

            // Late computation
            if ($inTime && $inTime->gt($schedIn->copy()->addMinutes($graceMinutes))) {
                $lateMinutes = (int) abs($schedIn->diffInMinutes($inTime));
                $status = AttendanceStatus::Late;
            }

            // Undertime computation
            if ($outTime && $outTime->lt($schedOut)) {
                $undertimeMinutes = (int) abs($schedOut->diffInMinutes($outTime));
                $status = ($status === AttendanceStatus::Late)
                    ? AttendanceStatus::LateAndUndertime
                    : AttendanceStatus::Undertime;
            }

            // Regular working hours
            if ($inTime && $outTime) {
                $totalMinutes = max(0, $inTime->diffInMinutes($outTime) - $breakMinutes);
                $regularHours = min(8.0, round($totalMinutes / 60, 2));
            }
        }

        // Check approved overtime for that date
        $otRequest = OvertimeRequest::where('employee_id', $employee->id)
            ->where('overtime_date', $recordDate->toDateString())
            ->where('status', 'approved')
            ->first();

        $overtimeHours = $otRequest ? (float) $otRequest->hours_requested : 0.0;

        return DailyTimeRecord::withoutGlobalScopes()->updateOrCreate(
            [
                'employee_id' => $employee->id,
                'record_date' => $recordDate->toDateString(),
            ],
            [
                'entity_id' => $employee->entity_id,
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'late_minutes' => $lateMinutes,
                'undertime_minutes' => $undertimeMinutes,
                'regular_hours' => $regularHours,
                'overtime_hours' => $overtimeHours,
                'status' => $status,
                'is_holiday' => (bool) $holiday,
                'holiday_id' => $holiday?->id,
                'leave_request_id' => $approvedLeave?->id,
                'overtime_request_id' => $otRequest?->id,
                'source' => $source,
                'remarks' => $remarks,
            ]
        );
    }
}
