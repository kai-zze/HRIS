<?php

namespace App\Filament\Resources\Positions\Tables;

use App\Models\Department;
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

class PositionsTable
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
                TextColumn::make('title')
                    ->label('Position Title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('Unassigned')
                    ->sortable(),
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->sortable(),
                TextColumn::make('job_grade')
                    ->label('Grade')
                    ->placeholder('—')
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
            ->defaultSort('title')
            ->filters([
                SelectFilter::make('entity_id')
                    ->label('Entity')
                    ->options(fn (): array => Entity::query()->orderBy('short_name')->pluck('short_name', 'id')->all())
                    ->visible(fn (): bool => Auth::user() instanceof User && Auth::user()->isSuperAdmin()),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all()),
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
            ->emptyStateHeading('No positions in current scope')
            ->emptyStateDescription('Define job titles and grades for your entity departments.');
    }
}
