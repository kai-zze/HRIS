<?php

namespace App\Filament\Resources\Departments\Tables;

use App\Models\Entity;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->sortable(),
                TextColumn::make('positions_count')
                    ->label('Positions')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('F d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('entity_id')
                    ->label('Entity')
                    ->options(fn (): array => Entity::query()->orderBy('short_name')->pluck('short_name', 'id')->all())
                    ->visible(fn (): bool => Auth::user() instanceof User && Auth::user()->isSuperAdmin()),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active departments')
                    ->falseLabel('Inactive departments'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No departments in current scope')
            ->emptyStateDescription('Create a department for this entity to organize positions and employees.');
    }
}
