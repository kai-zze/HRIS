<?php

namespace App\Filament\Employee\Widgets;

use App\Enums\RequestStatus;
use App\Models\Employee;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeLeaveStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        if (! $employee) {
            return [];
        }

        $currentYear = (int) now()->year;
        $credits = LeaveCredit::query()
            ->withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get();

        $vacationCredit = $credits->first(fn ($c) => str_contains(strtolower($c->leaveType?->name ?? ''), 'vacation'));
        $sickCredit = $credits->first(fn ($c) => str_contains(strtolower($c->leaveType?->name ?? ''), 'sick'));
        $emergencyCredit = $credits->first(fn ($c) => str_contains(strtolower($c->leaveType?->name ?? ''), 'emergency'));

        $pendingRequests = LeaveRequest::query()
            ->withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->where('status', RequestStatus::Pending)
            ->count();

        $stats = [];

        if ($vacationCredit) {
            $stats[] = Stat::make('Vacation Leave', sprintf('%.1f Days', $vacationCredit->remaining_days))
                ->description(sprintf('Allocated: %.1f | Used: %.1f', $vacationCredit->allocated_days, $vacationCredit->used_days))
                ->descriptionIcon('heroicon-m-sun')
                ->color('primary');
        }

        if ($sickCredit) {
            $stats[] = Stat::make('Sick Leave', sprintf('%.1f Days', $sickCredit->remaining_days))
                ->description(sprintf('Allocated: %.1f | Used: %.1f', $sickCredit->allocated_days, $sickCredit->used_days))
                ->descriptionIcon('heroicon-m-heart')
                ->color('success');
        }

        if ($emergencyCredit) {
            $stats[] = Stat::make('Emergency Leave', sprintf('%.1f Days', $emergencyCredit->remaining_days))
                ->description(sprintf('Allocated: %.1f | Used: %.1f', $emergencyCredit->allocated_days, $emergencyCredit->used_days))
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('warning');
        }

        $stats[] = Stat::make('Pending Requests', (string) $pendingRequests)
            ->description('Applications awaiting supervisor / HR action')
            ->descriptionIcon('heroicon-m-clock')
            ->color($pendingRequests > 0 ? 'warning' : 'gray');

        return $stats;
    }
}
