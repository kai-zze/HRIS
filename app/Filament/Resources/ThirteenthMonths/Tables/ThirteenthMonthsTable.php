<?php

namespace App\Filament\Resources\ThirteenthMonths\Tables;

use App\Models\ThirteenthMonthRecord;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ThirteenthMonthsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity.short_name')
                    ->label('Entity'),
                TextColumn::make('employee.employee_number')
                    ->label('Emp #')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('employee.last_name')
                    ->label('Employee')
                    ->state(fn (ThirteenthMonthRecord $record) => "{$record->employee->last_name}, {$record->employee->first_name}")
                    ->searchable(),
                TextColumn::make('year')
                    ->label('Year')
                    ->alignCenter(),
                TextColumn::make('total_basic_pay_earned')
                    ->label('Basic Earned')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('months_worked')
                    ->label('Months')
                    ->alignCenter(),
                TextColumn::make('thirteenth_month_pay')
                    ->label('13th Month Pay')
                    ->money('PHP')
                    ->weight('bold')
                    ->color('success')
                    ->alignRight(),
                TextColumn::make('tax_exempt_amount')
                    ->label('Tax Exempt (≤₱90k)')
                    ->money('PHP')
                    ->color('gray')
                    ->alignRight(),
                TextColumn::make('taxable_amount')
                    ->label('Taxable Excess')
                    ->money('PHP')
                    ->color(fn (ThirteenthMonthRecord $record) => $record->taxable_amount > 0 ? 'warning' : 'gray')
                    ->alignRight(),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->options([
                        2026 => '2026',
                        2025 => '2025',
                    ]),
            ])
            ->defaultSort('thirteenth_month_pay', 'desc');
    }
}
