<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_pinned')
                    ->label('Pinned')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->category),
                TextColumn::make('entity.name')
                    ->label('Target Entity')
                    ->default('Group-wide (All Entities)')
                    ->color(fn ($state) => $state === 'Group-wide (All Entities)' ? 'primary' : 'gray'),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'General' => 'General Notice',
                        'Policy' => 'Company Policy Update',
                        'Holiday' => 'Holiday & Schedule Advisory',
                        'Event' => 'Company Event / Townhall',
                        'Urgent' => 'Urgent Advisory',
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
