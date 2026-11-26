<?php

namespace App\Filament\Resources\EmployeeLoans\Tables;

use App\Models\EmployeeLoan;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.employee_number')
                    ->label('Emp #')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('employee.last_name')
                    ->label('Employee')
                    ->state(fn (EmployeeLoan $record) => "{$record->employee->last_name}, {$record->employee->first_name}")
                    ->searchable(),
                TextColumn::make('loan_type')
                    ->label('Loan Type')
                    ->sortable(),
                TextColumn::make('principal_amount')
                    ->label('Principal')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('amortization_per_cutoff')
                    ->label('Cutoff Deduction')
                    ->money('PHP')
                    ->color('warning')
                    ->alignRight(),
                TextColumn::make('balance_remaining')
                    ->label('Balance Remaining')
                    ->money('PHP')
                    ->weight('bold')
                    ->color(fn (EmployeeLoan $record) => $record->balance_remaining <= 0 ? 'success' : 'danger')
                    ->alignRight(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'paid' => 'Paid',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('loan_type')
                    ->options([
                        'SSS Salary Loan' => 'SSS Salary Loan',
                        'Pag-IBIG Multi-Purpose' => 'Pag-IBIG Multi-Purpose',
                        'Company Cash Advance' => 'Company Cash Advance',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->defaultSort('start_date', 'desc');
    }
}
