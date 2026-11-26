<?php

namespace App\Filament\Pages;

use App\Models\Entity;
use App\Services\ReportExportService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReportsCenter extends Page
{
    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | UnitEnum | null $navigationGroup = 'Reports & Compliance';

    protected static ?string $navigationLabel = 'Reports Center';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports-center';

    public ?int $selectedEntityId = null;
    public string $attendanceStartDate = '';
    public string $attendanceEndDate = '';
    public int $statutoryYear = 2026;
    public int $statutoryMonth = 1;

    public function mount(): void
    {
        $this->attendanceStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->attendanceEndDate = now()->format('Y-m-d');
        $this->statutoryYear = (int) date('Y');
        $this->statutoryMonth = (int) date('n');
    }

    public function downloadHeadcountReport(): StreamedResponse
    {
        Notification::make()->title('Exporting Headcount Report')->info()->send();
        return app(ReportExportService::class)->exportHeadcountReport($this->selectedEntityId);
    }

    public function downloadAttendanceReport(): StreamedResponse
    {
        Notification::make()->title('Exporting Attendance Report')->info()->send();
        return app(ReportExportService::class)->exportAttendanceReport(
            $this->attendanceStartDate,
            $this->attendanceEndDate,
            $this->selectedEntityId
        );
    }

    public function downloadStatutoryReport(): StreamedResponse
    {
        Notification::make()->title('Exporting Statutory Remittance Report')->info()->send();
        return app(ReportExportService::class)->exportStatutoryRemittanceReport(
            $this->statutoryYear,
            $this->statutoryMonth,
            $this->selectedEntityId
        );
    }

    public function downloadLeaveUtilizationReport(): StreamedResponse
    {
        Notification::make()->title('Exporting Leave Utilization Report')->info()->send();
        return app(ReportExportService::class)->exportLeaveUtilizationReport(
            $this->statutoryYear,
            $this->selectedEntityId
        );
    }
}
