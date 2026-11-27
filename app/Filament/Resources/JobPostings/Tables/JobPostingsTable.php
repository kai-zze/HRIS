<?php

namespace App\Filament\Resources\JobPostings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobPostingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Position Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => "{$record->department?->name} &bull; {$record->employment_type}"),
                TextColumn::make('entity.short_name')
                    ->label('Entity'),
                TextColumn::make('openings_count')
                    ->label('Slots')
                    ->numeric()
                    ->alignCenter(),
                TextColumn::make('applicants_count')
                    ->label('Applicants')
                    ->counts('applicants')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'Open' => 'success',
                        'Draft' => 'gray',
                        'On Hold' => 'warning',
                        'Closed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('posted_at')
                    ->label('Posted Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('posted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Open' => 'Open',
                        'On Hold' => 'On Hold',
                        'Closed' => 'Closed',
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
