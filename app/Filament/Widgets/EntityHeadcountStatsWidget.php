<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\Position;
use App\Services\EntityContextService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EntityHeadcountStatsWidget extends StatsOverviewWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $context = app(EntityContextService::class);
        $activeEntity = $context->activeEntity();
        $activeEntityId = $context->activeEntityId();

        $entityQuery = Entity::query()->where('is_active', true);
        if ($activeEntityId !== null && $activeEntityId > 0) {
            $entityQuery->whereKey($activeEntityId);
        }

        $activeEntitiesCount = $entityQuery->count();
        $departmentCount = Department::query()->where('is_active', true)->count();
        $positionCount = Position::query()->where('is_active', true)->count();

        // Real 201 Employee headcount
        $employeeQuery = Employee::query();
        if ($activeEntityId !== null && $activeEntityId > 0) {
            $employeeQuery->where('entity_id', $activeEntityId);
        }

        $totalEmployees = (clone $employeeQuery)->count();
        $regularEmployees = (clone $employeeQuery)->where('employment_status', 'regular')->count();

        $scopeLabel = $activeEntity
            ? $activeEntity->short_name
            : 'All 5 KM100 Entities';

        return [
            Stat::make('Active Entities in Scope', number_format($activeEntitiesCount))
                ->description($scopeLabel)
                ->descriptionIcon(Heroicon::OutlinedBuildingOffice2),
            Stat::make('Active Departments', number_format($departmentCount))
                ->description('Organizational units')
                ->descriptionIcon(Heroicon::OutlinedRectangleGroup),
            Stat::make('Active Positions', number_format($positionCount))
                ->description('Job titles & grades')
                ->descriptionIcon(Heroicon::OutlinedBriefcase),
            Stat::make('Employee 201 Headcount', number_format($totalEmployees))
                ->description(number_format($regularEmployees) . ' Regularized Employees')
                ->descriptionIcon(Heroicon::OutlinedIdentification),
        ];
    }
}
