<?php

namespace App\Filament\Widgets;

use App\Models\LeaveRequest;
use App\Services\EntityContextService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LeaveCalendarWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $activeEntityId = app(EntityContextService::class)->activeEntityId();

        return $table
            ->heading('Active & Upcoming Employee Leaves (Next 30 Days)')
            ->description('Live visibility into planned employee leaves to optimize daily team coverage across operations.')
            ->query(function () use ($activeEntityId): Builder {
                $query = LeaveRequest::query()
                    ->with(['employee.entity', 'employee.department', 'leaveType'])
                    ->where('status', 'approved')
                    ->whereDate('end_date', '>=', now()->toDateString())
                    ->whereDate('start_date', '<=', now()->addDays(30)->toDateString())
                    ->orderBy('start_date', 'asc');

                if ($activeEntityId !== null && $activeEntityId > 0) {
                    $query->whereHas('employee', fn ($q) => $q->where('entity_id', $activeEntityId));
                }

                return $query;
            })
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->weight('bold'),
                TextColumn::make('employee.entity.short_name')->label('Entity'),
                TextColumn::make('employee.department.name')->label('Department')->placeholder('—'),
                TextColumn::make('leaveType.name')->label('Leave Type'),
                TextColumn::make('start_date')->label('Start Date')->date('M d, Y (D)'),
                TextColumn::make('end_date')->label('End Date')->date('M d, Y (D)'),
                TextColumn::make('number_of_days')->label('Duration')->formatStateUsing(fn ($state) => "{$state}d")->weight('bold'),
                TextColumn::make('reason')->label('Reason / Notes')->limit(40),
            ])
            ->emptyStateHeading('No upcoming leaves scheduled')
            ->emptyStateDescription('All employees are scheduled on-duty for the next 30 days.')
            ->paginated([5, 10]);
    }
}
