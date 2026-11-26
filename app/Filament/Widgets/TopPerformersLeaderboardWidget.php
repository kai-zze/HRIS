<?php

namespace App\Filament\Widgets;

use App\Models\EmployeePerformanceRanking;
use App\Models\EvaluationPeriod;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopPerformersLeaderboardWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $latestPeriod = EvaluationPeriod::query()->latest('start_date')->first();

        return $table
            ->heading('Group Top Performers Leaderboard')
            ->description('Top ranked employees across all operating companies based on the 5-factor weighted index.')
            ->query(
                EmployeePerformanceRanking::withoutGlobalScopes()
                    ->when($latestPeriod, fn ($q) => $q->where('evaluation_period_id', $latestPeriod->id))
                    ->with(['employee', 'entity', 'department'])
                    ->orderBy('rank_overall')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('rank_overall')
                    ->label('Rank')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => '🥇 #1 Gold',
                            2 => '🥈 #2 Silver',
                            3 => '🥉 #3 Bronze',
                            null => '—',
                            default => "#{$state}",
                        };
                    })
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->weight('bold')
                    ->description(fn ($record) => $record->employee?->employee_number),

                TextColumn::make('entity.name')
                    ->label('Company'),

                TextColumn::make('department.name')
                    ->label('Department'),

                TextColumn::make('evaluation_score')
                    ->label('Eval')
                    ->numeric(1)
                    ->alignCenter(),

                TextColumn::make('attendance_score')
                    ->label('Att')
                    ->numeric(1)
                    ->alignCenter(),

                TextColumn::make('final_score')
                    ->label('Total Score')
                    ->numeric(1)
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->alignCenter(),
            ]);
    }
}
