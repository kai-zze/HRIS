<?php

namespace App\Filament\Resources\DailyTimeRecords\Tables;

use App\Exports\MonthlyAttendanceSummaryExport;
use App\Enums\AttendanceStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Services\AttendanceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DailyTimeRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_date')
                    ->label('Date')
                    ->date('M d, Y (D)')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('employee.employee_number')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('employee.entity.short_name')
                    ->label('Entity'),
                TextColumn::make('time_in')
                    ->label('Time In')
                    ->time('g:i A')
                    ->placeholder('—'),
                TextColumn::make('time_out')
                    ->label('Time Out')
                    ->time('g:i A')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('late_minutes')
                    ->label('Late')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}m" : '—')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('undertime_minutes')
                    ->label('UT')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}m" : '—')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('overtime_hours')
                    ->label('OT (Hrs)')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}h" : '—')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('record_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(AttendanceStatus::class),
                SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->options(fn () => Employee::all()->pluck('full_name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('exportMonthlySummary')
                    ->label('Monthly Summary (Excel)')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('success')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new MonthlyAttendanceSummaryExport(),
                            'KM100-Monthly-Attendance-Summary-' . date('Y-m') . '.xlsx'
                        );
                    }),
                Action::make('importBiometrics')
                    ->label('Import Biometric CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        FileUpload::make('csv_file')
                            ->label('Select Biometric / Timeclock CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = Storage::path($data['csv_file']);
                        if (! file_exists($filePath)) {
                            return;
                        }

                        $attendanceService = app(AttendanceService::class);
                        $imported = 0;

                        if (($handle = fopen($filePath, 'r')) !== false) {
                            $header = fgetcsv($handle); // employee_number, date, time_in, time_out
                            while (($row = fgetcsv($handle)) !== false) {
                                if (count($row) >= 4) {
                                    $empNumber = trim($row[0]);
                                    $date = trim($row[1]);
                                    $timeIn = trim($row[2]) ?: null;
                                    $timeOut = trim($row[3]) ?: null;

                                    $employee = Employee::withoutGlobalScopes()->where('employee_number', $empNumber)->first();
                                    if ($employee) {
                                        $attendanceService->computeAttendance(
                                            $employee,
                                            $date,
                                            $timeIn,
                                            $timeOut,
                                            'biometric',
                                            'Imported from Biometric Timeclock'
                                        );
                                        $imported++;
                                    }
                                }
                            }
                            fclose($handle);
                        }

                        Notification::make()
                            ->title("Biometrics Import Complete")
                            ->body("Processed {$imported} attendance punch records successfully.")
                            ->success()
                            ->send();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
