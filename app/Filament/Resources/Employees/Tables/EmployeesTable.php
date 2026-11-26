<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Enums\EmploymentStatus;
use App\Exports\EmployeesExport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_number')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?background=3b3b3d&color=f2851d&name=KM100'),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('entity.short_name')
                    ->label('Entity')
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('Unassigned')
                    ->sortable(),
                TextColumn::make('position.title')
                    ->label('Position')
                    ->placeholder('Unassigned')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('date_hired')
                    ->label('Date Hired')
                    ->date('F d, Y')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('entity_id')
                    ->label('Entity')
                    ->options(fn () => Entity::query()->pluck('short_name', 'id'))
                    ->visible(fn () => Auth::user() instanceof User && Auth::user()->isSuperAdmin()),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(fn () => Department::query()->pluck('name', 'id')),
                SelectFilter::make('employment_status')
                    ->label('Employment Status')
                    ->options(EmploymentStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('print201')
                    ->label('201 PDF')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->action(function (Employee $record) {
                        $pdf = Pdf::loadView('pdf.employee-201', ['employee' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "201-File-{$record->employee_number}.pdf"
                        );
                    }),
            ])
            ->toolbarActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new EmployeesExport(EmployeeResource::getEloquentQuery()), 'KM100-Employees-' . date('Y-m-d') . '.xlsx');
                    }),
                BulkActionGroup::make([
                    BulkAction::make('exportBulkExcel')
                        ->label('Export Selected to Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            return Excel::download(
                                new EmployeesExport(Employee::query()->whereIn('id', $records->pluck('id'))),
                                'KM100-Employees-Selected.xlsx'
                            );
                        }),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No employees found in current scope')
            ->emptyStateDescription('Click New Employee to enroll a 201 file record.');
    }
}
