<?php

namespace App\Filament\Resources\DisciplinaryRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DisciplinaryRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('Docket #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('entity.short_name')
                    ->label('Entity'),
                TextColumn::make('violation_category')
                    ->label('Category')
                    ->limit(25),
                TextColumn::make('sanction')
                    ->color(fn ($state) => match ($state) {
                        'Termination' => 'danger',
                        'Suspension' => 'danger',
                        'Written Reprimand' => 'warning',
                        'Written Warning' => 'warning',
                        'Exonerated' => 'success',
                        default => 'gray',
                    })
                    ->placeholder('Pending Decision'),
                TextColumn::make('penalty_points')
                    ->label('Penalty')
                    ->formatStateUsing(fn ($state) => "-{$state} pts")
                    ->color('danger')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'Sanction Imposed' => 'danger',
                        'Case Closed' => 'success',
                        'Hearing' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('incident_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Incident Reported' => 'Incident Reported',
                        'NTE Issued' => 'NTE Issued',
                        'Hearing' => 'Hearing',
                        'Sanction Imposed' => 'Sanction Imposed',
                        'Case Closed' => 'Case Closed',
                    ]),
                SelectFilter::make('entity_id')
                    ->relationship('entity', 'name')
                    ->label('Entity'),
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
