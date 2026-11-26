<?php

namespace App\Filament\Resources\TrainingPrograms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TrainingProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Program Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => "{$record->provider} &bull; {$record->duration_hours} hrs"),
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->default('Group-wide'),
                TextColumn::make('category'),
                TextColumn::make('participants_count')
                    ->label('Attendees')
                    ->counts('participants')
                    ->alignCenter(),
                TextColumn::make('trainer_name')
                    ->label('Trainer')
                    ->description(fn ($record) => $record->trainer_credentials),
                TextColumn::make('start_date')
                    ->label('Schedule')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Technical' => 'Technical',
                        'Safety' => 'Safety',
                        'Compliance' => 'Compliance',
                        'Leadership' => 'Leadership',
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
