<?php

namespace App\Filament\Resources\Entities\Tables;

use App\Models\Entity;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EntitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->getStateUsing(fn (Entity $record): string => $record->logo_url)
                    ->square()
                    ->imageSize(52)
                    ->extraImgAttributes([
                        'class' => 'object-contain',
                        'style' => 'border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; max-height: 48px; max-width: 70px; object-fit: contain;',
                    ]),
                TextColumn::make('code')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Entity Name')
                    ->description(fn (Entity $record): string => $record->short_name)
                    ->searchable(['name', 'short_name'])
                    ->sortable()
                    ->wrap(),
                TextColumn::make('departments_count')
                    ->label('Departments')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('positions_count')
                    ->label('Positions')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Accounts')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('tin_masked')
                    ->label('BIR TIN')
                    ->state(fn (Entity $record): string => Entity::maskIdentifier($record->tin))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->date('F d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active entities')
                    ->falseLabel('Inactive entities'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No corporate entities found')
            ->emptyStateDescription('Register a KM100 group company to configure its departments, positions, and users.');
    }
}
