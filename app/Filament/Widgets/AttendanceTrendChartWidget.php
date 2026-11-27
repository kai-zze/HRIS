<?php

namespace App\Filament\Widgets;

use App\Models\DailyTimeRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AttendanceTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Attendance & Tardiness Trend (Last 7 Days)';

    protected static ?int $sort = 5;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $records = DailyTimeRecord::withoutGlobalScopes()
            ->whereBetween('record_date', [$startDate, $endDate])
            ->selectRaw('DATE(record_date) as day, status, count(*) as count')
            ->groupBy('day', 'status')
            ->get();

        $grouped = [];
        foreach ($records as $row) {
            $dayKey = (string) $row->day;
            $statusKey = $row->status instanceof \BackedEnum ? $row->status->value : (string) $row->status;
            $grouped[$dayKey][$statusKey] = (int) $row->count;
        }

        $days = [];
        $onTime = [];
        $late = [];
        $absent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayKey = $date->format('Y-m-d');
            $days[] = $date->format('M d (D)');

            $onTime[] = $grouped[$dayKey]['present'] ?? 0;
            $late[] = $grouped[$dayKey]['late'] ?? 0;
            $absent[] = $grouped[$dayKey]['absent'] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Present on Time',
                    'data' => $onTime,
                    'borderColor' => '#10B981', // Emerald
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Tardy / Late',
                    'data' => $late,
                    'borderColor' => '#F2851D', // KM100 Orange
                    'backgroundColor' => 'rgba(242, 133, 29, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Absences',
                    'data' => $absent,
                    'borderColor' => '#EF4444', // Red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
