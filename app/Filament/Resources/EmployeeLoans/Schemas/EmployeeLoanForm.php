<?php

namespace App\Filament\Resources\EmployeeLoans\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Loan & Amortization Information')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'employee_number')
                            ->getOptionLabelFromRecordUsing(fn (Employee $emp) => "{$emp->employee_number} - {$emp->last_name}, {$emp->first_name}")
                            ->searchable()
                            ->required(),
                        Select::make('loan_type')
                            ->label('Loan Type / Source')
                            ->options([
                                'SSS Salary Loan' => 'SSS Salary Loan',
                                'SSS Calamity Loan' => 'SSS Calamity Loan',
                                'Pag-IBIG Multi-Purpose' => 'Pag-IBIG Multi-Purpose Loan (MPL)',
                                'Pag-IBIG Calamity' => 'Pag-IBIG Calamity Loan',
                                'Company Cash Advance' => 'Company Cash Advance',
                                'Emergency Loan' => 'Company Emergency Loan',
                            ])
                            ->required(),
                        TextInput::make('reference_number')
                            ->label('Reference / Billing No.')
                            ->placeholder('e.g. SSS-2026-998811'),
                        TextInput::make('principal_amount')
                            ->label('Principal Amount (₱)')
                            ->numeric()
                            ->prefix('₱')
                            ->required(),
                        TextInput::make('term_months')
                            ->label('Term (Months)')
                            ->numeric()
                            ->default(24)
                            ->required(),
                        TextInput::make('monthly_amortization')
                            ->label('Monthly Amortization (₱)')
                            ->numeric()
                            ->prefix('₱')
                            ->required(),
                        TextInput::make('amortization_per_cutoff')
                            ->label('Deduction per Semi-Monthly Cutoff (₱)')
                            ->numeric()
                            ->prefix('₱')
                            ->required(),
                        TextInput::make('balance_remaining')
                            ->label('Remaining Balance (₱)')
                            ->numeric()
                            ->prefix('₱')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'paid' => 'Fully Paid',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                        DatePicker::make('start_date')
                            ->label('Amortization Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Maturity / End Date'),
                    ]),
                    Textarea::make('notes')
                        ->label('Remarks / Deductions Instructions')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
