<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Employee;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('201 File Profile')
                    ->tabs([
                        Tab::make('Profile Overview')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Employee Identification')
                                    ->columns(4)
                                    ->schema([
                                        ImageEntry::make('photo_path')
                                            ->label('Photo')
                                            ->circular()
                                            ->defaultImageUrl('https://ui-avatars.com/api/?background=3b3b3d&color=f2851d&name=KM100'),
                                        TextEntry::make('employee_number')
                                            ->label('Employee ID')
                                            ->fontFamily('mono'),
                                        TextEntry::make('full_name')
                                            ->label('Full Name')
                                            ->weight('bold'),
                                        TextEntry::make('employment_status')
                                            ->label('Status'),
                                        TextEntry::make('entity.name')
                                            ->label('Entity'),
                                        TextEntry::make('department.name')
                                            ->label('Department')
                                            ->placeholder('—'),
                                        TextEntry::make('position.title')
                                            ->label('Position')
                                            ->placeholder('—'),
                                        TextEntry::make('supervisor.full_name')
                                            ->label('Supervisor')
                                            ->placeholder('—'),
                                    ]),

                                Section::make('Personal & Contact Info')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('birthdate')->date('F d, Y'),
                                        TextEntry::make('sex'),
                                        TextEntry::make('civil_status'),
                                        TextEntry::make('contact_number')->placeholder('—'),
                                        TextEntry::make('email')->placeholder('—'),
                                        TextEntry::make('residential_address')->columnSpanFull()->placeholder('—'),
                                    ]),

                                Section::make('Statutory Registrations (RA 10173 Masked)')
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('masked_tin')->label('BIR TIN'),
                                        TextEntry::make('masked_sss')->label('SSS Number'),
                                        TextEntry::make('masked_philhealth')->label('PhilHealth'),
                                        TextEntry::make('masked_pagibig')->label('Pag-IBIG MID'),
                                        TextEntry::make('bank_name')->label('Payroll Bank')->placeholder('—'),
                                        TextEntry::make('masked_bank_account')->label('Bank Account No.'),
                                        TextEntry::make('basic_salary')
                                            ->label('Basic Pay')
                                            ->money('PHP'),
                                    ]),
                            ]),

                        Tab::make('Movements & History')
                            ->icon('heroicon-o-arrows-right-left')
                            ->schema([
                                RepeatableEntry::make('movements')
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('effective_date')->date('F d, Y'),
                                        TextEntry::make('movement_type'),
                                        TextEntry::make('toEntity.short_name')->label('Destination Entity')->placeholder('—'),
                                        TextEntry::make('toPosition.title')->label('New Position')->placeholder('—'),
                                        TextEntry::make('new_salary')->label('Adjusted Salary')->money('PHP')->placeholder('—'),
                                        TextEntry::make('remarks')->columnSpanFull()->placeholder('—'),
                                    ]),
                            ]),

                        Tab::make('Uploaded Documents')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                RepeatableEntry::make('documents')
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('title')->label('Document Title')->weight('bold'),
                                        TextEntry::make('document_type'),
                                        TextEntry::make('issue_date')->date('F d, Y')->placeholder('—'),
                                        TextEntry::make('expiry_date')
                                            ->date('F d, Y')
                                            ->color(fn ($state) => $state && now()->parse($state)->isPast() ? 'danger' : 'warning')
                                            ->placeholder('No Expiration'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
