<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Assigned Users')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->date('F d, Y')
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateHeading('No roles configured')
            ->emptyStateDescription('Run the RolePermissionSeeder to initialize default KM100 roles.');
    }
}
