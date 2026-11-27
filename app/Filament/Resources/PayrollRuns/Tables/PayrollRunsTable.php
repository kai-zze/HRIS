<?php

namespace App\Filament\Resources\PayrollRuns\Tables;

use App\Models\PayrollRun;
use App\Services\BankExportService;
use App\Services\PayrollComputationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PayrollRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Run Name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('cutoff_period')
                    ->label('Cutoff Period')
                    ->state(fn (PayrollRun $record): string => sprintf(
                        '%s — %s',
                        $record->cutoff_start->format('M d, Y'),
                        $record->cutoff_end->format('M d, Y')
                    )),
                TextColumn::make('payout_date')
                    ->label('Payout Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'review' => 'warning',
                        'approved' => 'success',
                        'locked' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('total_employees')
                    ->label('Employees')
                    ->alignCenter(),
                TextColumn::make('total_gross')
                    ->label('Total Gross')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('total_deductions')
                    ->label('Deductions')
                    ->money('PHP')
                    ->alignRight(),
                TextColumn::make('total_net')
                    ->label('Total Net Pay')
                    ->money('PHP')
                    ->weight('bold')
                    ->color('success')
                    ->alignRight(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Under Review',
                        'approved' => 'Approved',
                        'locked' => 'Locked',
                    ]),
                SelectFilter::make('period_type')
                    ->options([
                        'first_half' => '1st Half',
                        'second_half' => '2nd Half',
                        'thirteenth_month' => '13th Month',
                        'special' => 'Special',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                ActionGroup::make([
                    Action::make('compute')
                        ->label('Generate / Recalculate')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Compute Payroll Items')
                        ->modalDescription('This will calculate basic pay, DTR attendance deductions, overtime, statutory contributions (SSS, PhilHealth, Pag-IBIG), withholding tax, and active loans for all active employees in this entity.')
                        ->action(function (PayrollRun $record) {
                            app(PayrollComputationService::class)->computePayrollRun($record);
                            Notification::make()
                                ->title('Payroll Computed Successfully')
                                ->body("Processed {$record->fresh()->total_employees} employees with Net Pay of ₱" . number_format($record->fresh()->total_net, 2))
                                ->success()
                                ->send();
                        }),

                    Action::make('approve')
                        ->label('Approve Payroll')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (PayrollRun $record) => in_array($record->status, ['draft', 'review']))
                        ->requiresConfirmation()
                        ->action(function (PayrollRun $record) {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => Auth::id(),
                                'approved_at' => now(),
                            ]);
                            Notification::make()
                                ->title('Payroll Approved')
                                ->success()
                                ->send();
                        }),

                    Action::make('lock')
                        ->label('Lock Run (Finalize)')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->visible(fn (PayrollRun $record) => $record->status === 'approved')
                        ->requiresConfirmation()
                        ->action(function (PayrollRun $record) {
                            $record->update(['status' => 'locked']);
                            Notification::make()
                                ->title('Payroll Run Locked')
                                ->body('No further edits or recalculations can be made.')
                                ->warning()
                                ->send();
                        }),

                    Action::make('export_bank')
                        ->label('Export Bank File (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(fn (PayrollRun $record) => app(BankExportService::class)->exportPayrollRunCsv($record)),

                    EditAction::make()
                        ->visible(fn (PayrollRun $record) => $record->status !== 'locked'),
                ]),
            ])
            ->defaultSort('cutoff_start', 'desc');
    }
}
