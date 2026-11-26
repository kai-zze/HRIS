<?php

namespace App\Filament\Resources\SssContributionBrackets\Tables;

use App\Models\SssContributionBracket;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SssContributionBracketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('compensation_range')
                    ->label('Compensation Range')
                    ->state(fn (SssContributionBracket $record) => $record->range_max
                        ? "₱" . number_format($record->range_min, 2) . " – ₱" . number_format($record->range_max, 2)
                        : "₱" . number_format($record->range_min, 2) . " and above"
                    )
                    ->weight('bold'),
                TextColumn::make('monthly_salary_credit')
                    ->label('MSC')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('regular_employee_share')
                    ->label('EE Share')
                    ->money('PHP')
                    ->color('warning')
                    ->alignRight(),
                TextColumn::make('regular_employer_share')
                    ->label('ER Share')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('ec_employer_share')
                    ->label('EC')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('wisp_employee_share')
                    ->label('WISP EE')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('wisp_employer_share')
                    ->label('WISP ER')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('total_ee')
                    ->label('Total EE Deduction')
                    ->state(fn (SssContributionBracket $record) => $record->regular_employee_share + $record->wisp_employee_share)
                    ->money('PHP')
                    ->weight('bold')
                    ->color('danger')
                    ->alignRight(),
                TextColumn::make('effective_year')
                    ->label('Year')
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('effective_year')
                    ->options([
                        2026 => '2026 Schedule',
                        2025 => '2025 Schedule',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->defaultSort('monthly_salary_credit', 'asc');
    }
}
