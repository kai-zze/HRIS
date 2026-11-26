<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\HolidayType;
use App\Enums\RequestStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\Holiday;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\WorkSchedule;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimeAttendanceLeaveSeeder extends Seeder
{
    public function run(): void
    {
        $entities = Entity::all();

        // 1. Work Schedules per entity
        foreach ($entities as $entity) {
            WorkSchedule::firstOrCreate(
                ['entity_id' => $entity->id, 'name' => "{$entity->short_name} Regular Day Shift"],
                [
                    'time_in' => '08:00:00',
                    'time_out' => '17:00:00',
                    'break_minutes' => 60,
                    'grace_period_minutes' => 15,
                    'working_days' => ['mon', 'tue', 'wed', 'thu', 'fri'],
                    'is_default' => true,
                    'is_active' => true,
                ]
            );
        }

        // 2. Standard Philippine Holidays (2026)
        $holidays = [
            ['name' => 'New Year\'s Day', 'date' => '2026-01-01', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Araw ng Kagitingan', 'date' => '2026-04-09', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Maundy Thursday', 'date' => '2026-04-02', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Good Friday', 'date' => '2026-04-03', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Labor Day', 'date' => '2026-05-01', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Independence Day', 'date' => '2026-06-12', 'type' => HolidayType::RegularHoliday],
            ['name' => 'National Heroes Day', 'date' => '2026-08-31', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Bonifacio Day', 'date' => '2026-11-30', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Christmas Day', 'date' => '2026-12-25', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Rizal Day', 'date' => '2026-12-30', 'type' => HolidayType::RegularHoliday],
            ['name' => 'Ninoy Aquino Day', 'date' => '2026-08-21', 'type' => HolidayType::SpecialNonWorking],
            ['name' => 'All Saints\' Day', 'date' => '2026-11-01', 'type' => HolidayType::SpecialNonWorking],
            ['name' => 'Feast of the Immaculate Conception', 'date' => '2026-12-08', 'type' => HolidayType::SpecialNonWorking],
        ];

        foreach ($holidays as $h) {
            Holiday::firstOrCreate(
                ['name' => $h['name'], 'holiday_date' => $h['date']],
                ['holiday_type' => $h['type'], 'description' => 'Official Philippine National Holiday']
            );
        }

        // 3. Philippine Statutory Leave Types
        $leaveTypes = [
            ['code' => 'VL', 'name' => 'Vacation Leave', 'credits' => 15, 'paid' => true, 'convertible' => true],
            ['code' => 'SL', 'name' => 'Sick Leave', 'credits' => 15, 'paid' => true, 'convertible' => true],
            ['code' => 'EL', 'name' => 'Emergency / Personal Leave', 'credits' => 5, 'paid' => true, 'convertible' => false],
            ['code' => 'ML', 'name' => 'Maternity Leave (RA 11210)', 'credits' => 105, 'paid' => true, 'convertible' => false],
            ['code' => 'PL', 'name' => 'Paternity Leave (RA 8187)', 'credits' => 7, 'paid' => true, 'convertible' => false],
            ['code' => 'SPL', 'name' => 'Solo Parent Leave (RA 8972)', 'credits' => 7, 'paid' => true, 'convertible' => false],
            ['code' => 'VAWC', 'name' => 'Battered Woman Leave (RA 9262)', 'credits' => 10, 'paid' => true, 'convertible' => false],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate(
                ['code' => $lt['code']],
                [
                    'name' => $lt['name'],
                    'default_credit_days' => $lt['credits'],
                    'is_paid' => $lt['paid'],
                    'is_convertible_to_cash' => $lt['convertible'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Populate Leave Credits for Employees
        $allEmployees = Employee::all();
        $vlType = LeaveType::where('code', 'VL')->first();
        $slType = LeaveType::where('code', 'SL')->first();
        $elType = LeaveType::where('code', 'EL')->first();

        foreach ($allEmployees as $emp) {
            foreach ([$vlType, $slType, $elType] as $type) {
                if ($type) {
                    LeaveCredit::firstOrCreate(
                        ['employee_id' => $emp->id, 'leave_type_id' => $type->id, 'year' => 2026],
                        [
                            'allocated_days' => $type->default_credit_days,
                            'used_days' => rand(1, 4),
                            'pending_days' => 0,
                            'remaining_days' => $type->default_credit_days - 3,
                        ]
                    );
                }
            }
        }

        // 5. Sample Leave Requests
        $sampleEmployee = Employee::first();
        if ($sampleEmployee && $vlType) {
            LeaveRequest::firstOrCreate(
                [
                    'employee_id' => $sampleEmployee->id,
                    'start_date' => now()->addDays(5)->toDateString(),
                ],
                [
                    'leave_type_id' => $vlType->id,
                    'end_date' => now()->addDays(7)->toDateString(),
                    'number_of_days' => 3,
                    'reason' => 'Annual family vacation / personal wellness rest.',
                    'status' => RequestStatus::Approved,
                    'manager_action_at' => now()->subDay(),
                    'hr_action_at' => now(),
                ]
            );
        }

        // 6. Sample Overtime Requests
        if ($sampleEmployee) {
            OvertimeRequest::firstOrCreate(
                [
                    'employee_id' => $sampleEmployee->id,
                    'overtime_date' => now()->subDays(2)->toDateString(),
                ],
                [
                    'start_time' => '17:00:00',
                    'end_time' => '20:00:00',
                    'hours_requested' => 3.0,
                    'purpose' => 'Urgent TESDA batch document compilation and compliance filing.',
                    'status' => RequestStatus::Approved,
                    'manager_action_at' => now()->subDays(2),
                    'hr_action_at' => now()->subDays(2),
                ]
            );
        }

        // 7. Seed Past 5 Days of DTR Attendance for Employees
        $attendanceService = app(AttendanceService::class);
        foreach ($allEmployees->take(15) as $emp) {
            for ($i = 5; $i >= 1; $i--) {
                $date = now()->subDays($i);
                if ($date->isWeekend()) {
                    continue;
                }

                $punchVariations = [
                    ['in' => '07:55:00', 'out' => '17:05:00'],
                    ['in' => '08:00:00', 'out' => '17:00:00'],
                    ['in' => '08:25:00', 'out' => '17:00:00'], // Late
                    ['in' => '07:50:00', 'out' => '16:30:00'], // Undertime
                ];
                $punch = $punchVariations[array_rand($punchVariations)];

                $attendanceService->computeAttendance(
                    $emp,
                    $date->toDateString(),
                    $punch['in'],
                    $punch['out'],
                    'biometric',
                    'Timeclock auto-sync'
                );
            }
        }
    }
}
