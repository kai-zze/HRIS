<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\RequestStatus;
use App\Enums\RoleName;
use App\Filament\Employee\Pages\CompanyAnnouncements;
use App\Filament\Employee\Pages\MyProfile;
use App\Filament\Employee\Resources\MyDailyTimeRecords\Pages\ListMyDailyTimeRecords;
use App\Filament\Employee\Resources\MyLeaveRequests\Pages\CreateMyLeaveRequest;
use App\Filament\Employee\Resources\MyLeaveRequests\Pages\ListMyLeaveRequests;
use App\Filament\Employee\Resources\MyOvertimeRequests\Pages\CreateMyOvertimeRequest;
use App\Models\Announcement;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Phase4EmployeeSelfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Employee $employee;
    protected Entity $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $this->user = User::query()->where('email', 'emp1.km100@km100group.ph')->first()
            ?? User::query()->whereHas('employee')->firstOrFail();
        $this->employee = $this->user->employee;
        $this->entity = $this->employee->entity;
    }

    public function test_employee_can_access_portal_dashboard(): void
    {
        $this->actingAs($this->user)
            ->get('/portal')
            ->assertSuccessful();
    }

    public function test_employee_can_view_and_update_profile_and_emergency_contacts(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MyProfile::class)
            ->assertSuccessful()
            ->set('data.contact_number', '+63 999 888 7777')
            ->set('data.residential_address', 'Updated Address, Makati City')
            ->set('data.emergency_contacts', [
                [
                    'name' => 'Maria Dela Cruz',
                    'relationship' => 'Spouse',
                    'contact_number' => '+63 917 555 6666',
                    'is_primary' => true,
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->employee->refresh();
        $this->assertEquals('+63 999 888 7777', $this->employee->contact_number);
        $this->assertEquals('Updated Address, Makati City', $this->employee->residential_address);

        $this->assertDatabaseHas('emergency_contacts', [
            'employee_id' => $this->employee->id,
            'name' => 'Maria Dela Cruz',
            'relationship' => 'Spouse',
        ]);
    }

    public function test_employee_can_only_view_own_daily_time_records(): void
    {
        $otherEmployee = Employee::withoutGlobalScopes()->where('id', '!=', $this->employee->id)->firstOrFail();

        $myDtr = DailyTimeRecord::withoutGlobalScopes()->create([
            'employee_id' => $this->employee->id,
            'entity_id' => $this->entity->id,
            'record_date' => today()->toDateString(),
            'status' => AttendanceStatus::Present,
            'regular_hours' => 8.0,
        ]);

        $otherDtr = DailyTimeRecord::withoutGlobalScopes()->create([
            'employee_id' => $otherEmployee->id,
            'entity_id' => $otherEmployee->entity_id,
            'record_date' => today()->toDateString(),
            'status' => AttendanceStatus::Late,
            'regular_hours' => 7.5,
        ]);

        $this->actingAs($this->user);

        Livewire::test(ListMyDailyTimeRecords::class)
            ->assertCanSeeTableRecords([$myDtr])
            ->assertCanNotSeeTableRecords([$otherDtr]);
    }

    public function test_employee_can_file_leave_request_with_sufficient_balance(): void
    {
        $leaveType = LeaveType::withoutGlobalScopes()->where('code', 'VL')->first()
            ?? LeaveType::withoutGlobalScopes()->firstOrFail();

        LeaveCredit::withoutGlobalScopes()->updateOrCreate(
            [
                'employee_id' => $this->employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => (int) now()->year,
            ],
            [
                'allocated_days' => 15,
                'used_days' => 0,
                'pending_days' => 0,
            ]
        );

        $this->actingAs($this->user);

        Livewire::test(CreateMyLeaveRequest::class)
            ->fillForm([
                'leave_type_id' => $leaveType->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
                'reason' => 'Family vacation to province',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'leave_type_id' => $leaveType->id,
            'status' => RequestStatus::Pending->value,
            'reason' => 'Family vacation to province',
        ]);
    }

    public function test_leave_filing_fails_when_credits_are_insufficient(): void
    {
        $leaveType = LeaveType::withoutGlobalScopes()->where('code', 'VL')->first()
            ?? LeaveType::withoutGlobalScopes()->firstOrFail();

        LeaveCredit::withoutGlobalScopes()->updateOrCreate(
            [
                'employee_id' => $this->employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => (int) now()->year,
            ],
            [
                'allocated_days' => 1,
                'used_days' => 1, // 0 remaining
                'remaining_days' => 0,
                'pending_days' => 0,
            ]
        );

        $this->actingAs($this->user);

        Livewire::test(CreateMyLeaveRequest::class)
            ->fillForm([
                'leave_type_id' => $leaveType->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(7)->toDateString(), // 3 days requested
                'reason' => 'Trying to exceed balance',
            ])
            ->call('create')
            ->assertHasFormErrors(['leave_type_id']);
    }

    public function test_employee_can_file_overtime_request(): void
    {
        $this->actingAs($this->user);

        Livewire::test(CreateMyOvertimeRequest::class)
            ->fillForm([
                'overtime_date' => today()->toDateString(),
                'start_time' => '17:00',
                'end_time' => '20:00',
                'purpose' => 'Urgent month-end reporting deadline',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('overtime_requests', [
            'employee_id' => $this->employee->id,
            'status' => RequestStatus::Pending->value,
            'hours_requested' => 3.0,
            'purpose' => 'Urgent month-end reporting deadline',
        ]);
    }

    public function test_employee_sees_only_own_entity_or_group_wide_announcements(): void
    {
        $otherEntity = Entity::query()->where('id', '!=', $this->entity->id)->firstOrFail();

        $groupWideNotice = Announcement::query()->create([
            'entity_id' => null,
            'title' => 'Group-wide Townhall Notice',
            'content' => '<p>Townhall for everyone</p>',
            'category' => 'Event',
            'published_at' => now()->subDay(),
        ]);

        $myEntityNotice = Announcement::query()->create([
            'entity_id' => $this->entity->id,
            'title' => 'Test Entity Staff Meeting',
            'content' => '<p>Meeting for Test Entity</p>',
            'category' => 'General',
            'published_at' => now()->subHour(),
        ]);

        $otherEntityNotice = Announcement::query()->create([
            'entity_id' => $otherEntity->id,
            'title' => 'Other Entity Confidential Notice',
            'content' => '<p>Secret notice</p>',
            'category' => 'Urgent',
            'published_at' => now()->subHour(),
        ]);

        $this->actingAs($this->user);

        $page = new CompanyAnnouncements;
        $visibleTitles = $page->getAnnouncementsProperty()->pluck('title')->toArray();

        $this->assertContains('Group-wide Townhall Notice', $visibleTitles);
        $this->assertContains('Test Entity Staff Meeting', $visibleTitles);
        $this->assertNotContains('Other Entity Confidential Notice', $visibleTitles);
    }
}
