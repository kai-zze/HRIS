<?php

namespace App\Filament\Widgets;

use App\Models\Entity;
use App\Services\EntityContextService;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class EntityOverviewTableWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $activeEntityId = app(EntityContextService::class)->activeEntityId();

        return $table
            ->heading('Headcount & Structure by KM100 Entity')
            ->description('Live headcount and 201 employee distribution across all corporate group companies.')
            ->query(function () use ($activeEntityId): Builder {
                $query = Entity::query()
                    ->withCount([
                        'departments' => fn (Builder $q) => $q->where('is_active', true),
                        'positions' => fn (Builder $q) => $q->where('is_active', true),
                        'employees',
                        'users as total_accounts' => fn (Builder $q) => $q->where('is_active', true),
                    ])
                    ->orderBy('id');

                if ($activeEntityId !== null && $activeEntityId > 0) {
                    $query->whereKey($activeEntityId);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->fontFamily('mono'),
                TextColumn::make('name')
                    ->label('Company / Entity')
                    ->description(fn (Entity $record): string => $record->short_name)
                    ->wrap(),
                TextColumn::make('departments_count')
                    ->label('Departments')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('positions_count')
                    ->label('Positions')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('employees_count')
                    ->label('201 Headcount')
                    ->numeric()
                    ->alignEnd()
                    ->weight('bold'),
                TextColumn::make('total_accounts')
                    ->label('User Accounts')
                    ->numeric()
                    ->alignEnd(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->paginated(false);
    }
}
