<?php

namespace App\Filament\Resources\EmployeePerformanceRankings\Tables;

use App\Services\PerformanceRankingService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeePerformanceRankingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rank_overall')
                    ->label('Rank')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => '🥇 #1',
                            2 => '🥈 #2',
                            3 => '🥉 #3',
                            null => '—',
                            default => "#{$state}",
                        };
                    })
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'last_name', 'employee_number'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => "{$record->employee?->employee_number} &bull; {$record->department?->name}"),

                TextColumn::make('entity.short_name')
                    ->label('Entity'),

                TextColumn::make('evaluation_score')
                    ->label('Eval (50%)')
                    ->numeric(1)
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('attendance_score')
                    ->label('Att (20%)')
                    ->numeric(1)
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('training_score')
                    ->label('Train (15%)')
                    ->numeric(1)
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('discipline_score')
                    ->label('Disc (10%)')
                    ->numeric(1)
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('commendation_score')
                    ->label('Comm (5%)')
                    ->numeric(1)
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('calculated_total_score')
                    ->label('Calc Total')
                    ->numeric(2)
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('manual_adjustment_score')
                    ->label('HR Adj.')
                    ->formatStateUsing(function ($record) {
                        $val = (float) $record->manual_adjustment_score;
                        if ($val == 0) return '—';
                        return sprintf('%+0.1f pts', $val);
                    })
                    ->tooltip(fn ($record) => $record->manual_adjustment_reason ?: 'No manual adjustments applied.')
                    ->alignCenter(),

                TextColumn::make('final_score')
                    ->label('Final Score')
                    ->numeric(2)
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('rank_department')
                    ->label('Dept Rank')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '—')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('rank_position')
                    ->label('Pos Rank')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '—')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->defaultSort('final_score', 'desc')
            ->filters([
                SelectFilter::make('evaluation_period_id')
                    ->relationship('evaluationPeriod', 'name')
                    ->label('Evaluation Period'),
                SelectFilter::make('entity_id')
                    ->relationship('entity', 'name')
                    ->label('Entity'),
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department'),
            ])
            ->actions([
                // HR Manual Score Adjustment Action
                Action::make('adjustScore')
                    ->label('Adjust Score')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        TextInput::make('manual_adjustment_score')
                            ->label('Adjustment Points (+ or -)')
                            ->numeric()
                            ->required()
                            ->helperText('e.g. +2.5 for exceptional client commendation, or -3.0 for unrecorded infraction.')
                            ->default(fn ($record) => $record->manual_adjustment_score),
                        Textarea::make('manual_adjustment_reason')
                            ->label('Justification / Audit Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('State the detailed management justification for modifying the algorithmic score.')
                            ->default(fn ($record) => $record->manual_adjustment_reason),
                    ])
                    ->action(function ($record, array $data) {
                        $service = app(PerformanceRankingService::class);
                        $service->applyManualAdjustment($record, (float)$data['manual_adjustment_score'], $data['manual_adjustment_reason']);

                        Notification::make()
                            ->title('Score Adjusted & Rankings Recalculated')
                            ->body("Updated final score for {$record->employee?->full_name}.")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
