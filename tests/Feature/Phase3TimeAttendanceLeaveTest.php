<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\LeaveBalanceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3TimeAttendanceLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_time_and_leave_seeders_populate_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(5, Holiday::count());
        $this->assertGreaterThan(3, LeaveType::count());
        $this->assertGreaterThan(5, LeaveCredit::count());
        $this->assertGreaterThan(10, DailyTimeRecord::withoutGlobalScopes()->count());
    }

    public function test_attendance_service_computes_late_and_undertime(): void
    {
        $this->seed(DatabaseSeeder::class);

        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        $service = app(AttendanceService::class);

        // 8:30 AM arrival (sched is 8:00 AM + 15m grace period) = 30 min late
        $dtr = $service->computeAttendance($employee, '2026-11-20', '08:30:00', '17:00:00');

        $this->assertSame(AttendanceStatus::Late, $dtr->status);
        $this->assertSame(30, $dtr->late_minutes);
        $this->assertSame(0, $dtr->undertime_minutes);
    }

    public function test_leave_balance_service_deducts_upon_approval(): void
    {
        $this->seed(DatabaseSeeder::class);

        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        $leaveType = LeaveType::where('code', 'VL')->firstOrFail();
        $service = app(LeaveBalanceService::class);

        $service->allocateYearlyCredits($employee, 2026);

        $initialCredit = LeaveCredit::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', 2026)
            ->firstOrFail();

        $initialRemaining = (float) $initialCredit->remaining_days;

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-11-23',
            'end_date' => '2026-11-24',
            'number_of_days' => 2.0,
            'reason' => 'Test Vacation',
            'status' => 'approved',
        ]);

        $service->deductBalance($leaveRequest);

        $updatedCredit = LeaveCredit::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', 2026)
            ->firstOrFail();

        $this->assertEquals((float) $initialCredit->used_days + 2.0, (float) $updatedCredit->used_days);
    }
}
