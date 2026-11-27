<?php

namespace App\Filament\Widgets;

use App\Models\EmployeeDocument;
use App\Services\EntityContextService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DocumentExpiryWidget extends TableWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $activeEntityId = app(EntityContextService::class)->activeEntityId();

        return $table
            ->heading('Expiring Employee Documents & Certifications (Next 60 Days)')
            ->description('Monitors upcoming expiration for contracts, NBI clearances, medical certificates, and TESDA credentials.')
            ->query(function () use ($activeEntityId): Builder {
                $query = EmployeeDocument::query()
                    ->with(['employee.entity', 'employee.department'])
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays(60))
                    ->orderBy('expiry_date', 'asc');

                if ($activeEntityId !== null && $activeEntityId > 0) {
                    $query->whereHas('employee', fn (Builder $q) => $q->where('entity_id', $activeEntityId));
                }

                return $query;
            })
            ->columns([
                TextColumn::make('employee.employee_number')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('employee.entity.short_name')
                    ->label('Entity'),
                TextColumn::make('document_type')
                    ->label('Document Type'),
                TextColumn::make('title')
                    ->label('Document Title')
                    ->wrap(),
                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date('F d, Y')
                    ->sortable()
                    ->color(fn (EmployeeDocument $record): string => $record->isExpired() ? 'danger' : 'warning')
                    ->weight('bold'),
                TextColumn::make('days_remaining')
                    ->label('Status')
                    ->state(function (EmployeeDocument $record): string {
                        if ($record->isExpired()) {
                            return 'Expired (' . $record->expiry_date->diffForHumans() . ')';
                        }

                        $days = (int) now()->diffInDays($record->expiry_date, false);

                        return $days === 0 ? 'Expires today' : "Expires in {$days} days";
                    })
                    ->color(fn (EmployeeDocument $record): string => $record->isExpired() ? 'danger' : 'warning'),
            ])
            ->emptyStateHeading('No documents expiring soon')
            ->emptyStateDescription('All employee contracts, medical certificates, and clearances are currently up to date.')
            ->paginated([5, 10]);
    }
}
