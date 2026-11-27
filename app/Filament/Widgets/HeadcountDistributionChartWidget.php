<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Entity;
use Filament\Widgets\ChartWidget;

class HeadcountDistributionChartWidget extends ChartWidget
{
    protected ?string $heading = 'Group Headcount by Operating Entity';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $counts = Employee::withoutGlobalScopes()
            ->whereNotIn('employment_status', ['resigned', 'terminated'])
            ->selectRaw('entity_id, count(*) as count')
            ->groupBy('entity_id')
            ->pluck('count', 'entity_id')
            ->all();

        $entities = Entity::all(['id', 'short_name', 'code']);
        $labels = [];
        $data = [];

        foreach ($entities as $entity) {
            $labels[] = $entity->short_name ?: $entity->code;
            $data[] = (int) ($counts[$entity->id] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Active Employees',
                    'data' => $data,
                    'backgroundColor' => [
                        '#F2851D', // KM100 Orange
                        '#C2510A', // Dark Orange
                        '#3B3B3D', // Charcoal
                        '#52525B', // Zinc 600
                        '#A1A1AA', // Zinc 400
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
