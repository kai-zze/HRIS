<?php

namespace Tests\Feature;

use App\Filament\Pages\ReportsCenter;
use App\Filament\Pages\SystemSettings;
use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Filament\Widgets\AttendanceTrendChartWidget;
use App\Filament\Widgets\HeadcountDistributionChartWidget;
use App\Filament\Widgets\MonthlyPayrollExpensesChartWidget;
use App\Models\Employee;
use App\Models\User;
use App\Services\ReportExportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class Phase7ReportsAnalyticsAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_activity_logs_track_changes_and_diff_properties(): void
    {
        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        $admin = User::where('email', 'superadmin@km100group.ph')->firstOrFail();

        $this->actingAs($admin);
        $employee->update(['first_name' => 'UpdatedFirstName']);

        $activity = Activity::where('subject_type', Employee::class)
            ->where('subject_id', $employee->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('updated', $activity->description);

        $this->get(ActivityLogResource::getUrl('index'))->assertOk();
    }

    public function test_executive_analytics_and_chart_widgets_produce_valid_data(): void
    {
        $headcountWidget = new class extends HeadcountDistributionChartWidget {
            public function exposeData(): array { return $this->getData(); }
        };
        $hcData = $headcountWidget->exposeData();
        $this->assertArrayHasKey('datasets', $hcData);
        $this->assertArrayHasKey('labels', $hcData);
        $this->assertNotEmpty($hcData['labels']);

        $payrollWidget = new class extends MonthlyPayrollExpensesChartWidget {
            public function exposeData(): array { return $this->getData(); }
        };
        $payData = $payrollWidget->exposeData();
        $this->assertArrayHasKey('datasets', $payData);
        $this->assertArrayHasKey('labels', $payData);

        $attendanceWidget = new class extends AttendanceTrendChartWidget {
            public function exposeData(): array { return $this->getData(); }
        };
        $attData = $attendanceWidget->exposeData();
        $this->assertArrayHasKey('datasets', $attData);
        $this->assertCount(3, $attData['datasets']); // Present, Late, Absent
    }

    public function test_report_export_service_generates_headcount_csv(): void
    {
        $service = app(ReportExportService::class);
        $response = $service->exportHeadcountReport();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KM100-Headcount-Report-', $response->headers->get('Content-Disposition'));
    }

    public function test_report_export_service_generates_attendance_csv(): void
    {
        $service = app(ReportExportService::class);
        $response = $service->exportAttendanceReport('2026-01-01', '2026-01-31');

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KM100-Attendance-Report-', $response->headers->get('Content-Disposition'));
    }

    public function test_report_export_service_generates_statutory_remittance_csv(): void
    {
        $service = app(ReportExportService::class);
        $response = $service->exportStatutoryRemittanceReport(2026, 1);

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KM100-Statutory-Remittance-', $response->headers->get('Content-Disposition'));
    }

    public function test_report_export_service_generates_leave_utilization_csv(): void
    {
        $service = app(ReportExportService::class);
        $response = $service->exportLeaveUtilizationReport(2026);

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KM100-Leave-Utilization-', $response->headers->get('Content-Disposition'));
    }

    public function test_reports_center_and_system_settings_pages_render(): void
    {
        $admin = User::where('email', 'superadmin@km100group.ph')->firstOrFail();
        $this->actingAs($admin);

        $this->get('/admin/reports-center')->assertOk();
        $this->get('/admin/system-settings')->assertOk();
    }
}
