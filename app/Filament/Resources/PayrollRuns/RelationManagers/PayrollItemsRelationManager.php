<?php

namespace App\Filament\Resources\PayrollRuns\RelationManagers;

use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Employee Payroll Items & Payslips';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('employee.employee_number')
                    ->label('Emp #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('employee_name')
                    ->label('Employee Name')
                    ->state(fn (PayrollItem $record) => sprintf(
                        '%s, %s %s',
                        $record->employee->last_name,
                        $record->employee->first_name,
                        $record->employee->suffix ?? ''
                    ))
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('basic_pay_earned')
                    ->label('Basic Pay')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('attendance_deductions')
                    ->label('Late/Absent')
                    ->state(fn (PayrollItem $record) => $record->absent_deduction + $record->late_deduction + $record->undertime_deduction)
                    ->money('PHP')
                    ->color('danger')
                    ->alignRight(),
                TextColumn::make('total_ot')
                    ->label('OT / Holiday')
                    ->state(fn (PayrollItem $record) => $record->regular_ot_pay + $record->rest_day_ot_pay + $record->holiday_ot_pay)
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('gross_pay')
                    ->label('Gross Pay')
                    ->money('PHP')
                    ->weight('bold')
                    ->alignRight(),
                TextColumn::make('total_statutory')
                    ->label('Statutory EE')
                    ->state(fn (PayrollItem $record) => $record->sss_ee + $record->sss_wisp_ee + $record->philhealth_ee + $record->pagibig_ee)
                    ->money('PHP')
                    ->color('warning')
                    ->alignRight(),
                TextColumn::make('withholding_tax')
                    ->label('W/Tax')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('loan_deductions')
                    ->label('Loans')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('total_deductions')
                    ->label('Total Ded.')
                    ->money('PHP')
                    ->color('danger')
                    ->alignRight(),
                TextColumn::make('net_pay')
                    ->label('Net Take-Home')
                    ->money('PHP')
                    ->weight('bold')
                    ->color('success')
                    ->alignRight(),
            ])
            ->actions([
                Action::make('download_payslip')
                    ->label('PDF Payslip')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (PayrollItem $record) {
                        $pdf = Pdf::loadView('pdf.payslip', ['item' => $record]);
                        $filename = sprintf(
                            'Payslip-%s-%s.pdf',
                            $record->employee->employee_number,
                            $record->payrollRun->payout_date->format('Ymd')
                        );

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            $filename
                        );
                    }),
            ])
            ->defaultSort('net_pay', 'desc');
    }
}
