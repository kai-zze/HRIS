<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleName;
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

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->placeholder('All Entities')
                    ->badge(false)
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge(false)
                    ->weight('medium'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label('Last Sign-In')
                    ->dateTime('F d, Y g:i A')
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('entity_id')
                    ->label('Entity')
                    ->options(fn (): array => Entity::query()->orderBy('short_name')->pluck('short_name', 'id')->all())
                    ->visible(fn (): bool => Auth::user() instanceof User && Auth::user()->isSuperAdmin()),
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Status'),
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
            ->emptyStateHeading('No user accounts found')
            ->emptyStateDescription('Create an account and assign its entity and role.');
    }
}
