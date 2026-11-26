<?php

namespace App\Filament\Resources\PerformanceEvaluations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PerformanceEvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->employee?->position?->title),
                TextColumn::make('evaluationPeriod.name')
                    ->label('Period')
                    ->limit(25),
                TextColumn::make('evaluator.name')
                    ->label('Evaluator')
                    ->toggleable(),
                TextColumn::make('evaluation_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state === 'probation_review' ? 'Probation Review' : 'Regular KPI'),
                TextColumn::make('score')
                    ->label('Score')
                    ->numeric(1)
                    ->suffix(' pts')
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->alignCenter(),
                TextColumn::make('status'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('evaluation_period_id')
                    ->relationship('evaluationPeriod', 'name')
                    ->label('Period'),
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Submitted' => 'Submitted',
                        'Approved' => 'Approved',
                    ]),
            ])
            ->actions([
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
