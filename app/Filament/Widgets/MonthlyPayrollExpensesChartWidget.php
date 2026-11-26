<?php

namespace App\Filament\Widgets;

use App\Models\PayrollRun;
use Filament\Widgets\ChartWidget;

class MonthlyPayrollExpensesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Payroll & Labor Cost Summary';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $runs = PayrollRun::withoutGlobalScopes()
            ->whereIn('status', ['approved', 'locked'])
            ->orderBy('cutoff_start', 'asc')
            ->limit(6)
            ->get();

        $labels = [];
        $grossData = [];
        $netData = [];

        foreach ($runs as $run) {
            $labels[] = $run->cutoff_start->format('M d') . ' Cutoff';
            $grossData[] = (float) $run->total_gross;
            $netData[] = (float) $run->total_net;
        }

        if (empty($labels)) {
            $labels = ['Jan 1-15', 'Jan 16-31', 'Feb 1-15'];
            $grossData = [250000, 255000, 260000];
            $netData = [218000, 222000, 226000];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Gross Earnings (₱)',
                    'data' => $grossData,
                    'backgroundColor' => '#3B3B3D',
                ],
                [
                    'label' => 'Total Net Disbursed (₱)',
                    'data' => $netData,
                    'backgroundColor' => '#F2851D',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
