<?php

namespace App\Filament\Resources\EvaluationPeriods\Tables;

use App\Services\PerformanceRankingService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EvaluationPeriodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Cycle Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => sprintf('%s to %s', $record->start_date->format('M d, Y'), $record->end_date->format('M d, Y'))),
                TextColumn::make('entity.short_name')
                    ->label('Entity Scope')
                    ->default('Group-wide'),
                TextColumn::make('period_type')
                    ->label('Cadence')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('rankings_count')
                    ->label('Ranked Staff')
                    ->counts('rankings')
                    ->alignCenter(),
                TextColumn::make('finalized_at')
                    ->label('Finalized')
                    ->dateTime('M d, Y')
                    ->placeholder('Open for adjustments'),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Active' => 'Active',
                        'Closed' => 'Closed',
                        'Finalized' => 'Finalized',
                    ]),
            ])
            ->actions([
                // Compute / Recalculate Rankings Action
                Action::make('computeRankings')
                    ->label('Calculate Rankings')
                    ->icon('heroicon-m-calculator')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Compute 5-Factor Performance Rankings')
                    ->modalDescription('This will process evaluation scores, attendance tardiness records, training certifications, disciplinary sanctions, and commendations for all eligible employees.')
                    ->action(function ($record) {
                        $service = app(PerformanceRankingService::class);
                        $rankings = $service->computePeriodRankings($record);

                        Notification::make()
                            ->title('Rankings Computed Successfully')
                            ->body("Calculated weighted rankings for {$rankings->count()} employees.")
                            ->success()
                            ->send();
                    }),

                // Finalize Period Action
                Action::make('finalize')
                    ->label('Finalize Period')
                    ->icon('heroicon-m-lock-closed')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'Finalized')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $service = app(PerformanceRankingService::class);
                        $service->finalizePeriod($record, auth()->id());

                        Notification::make()
                            ->title('Period Finalized')
                            ->body('Evaluation cycle has been locked and rankings are finalized.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
