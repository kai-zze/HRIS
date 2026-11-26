<?php

namespace App\Filament\Resources\DailyTimeRecords\Schemas;

use App\Enums\AttendanceStatus;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DailyTimeRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Entry')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn () => Employee::all()->pluck('full_name', 'id'))
                            ->required()
                            ->searchable(),
                        DatePicker::make('record_date')
                            ->label('Date')
                            ->required()
                            ->native(false)
                            ->default(now()),
                        Select::make('status')
                            ->options(AttendanceStatus::class)
                            ->required()
                            ->default(AttendanceStatus::Present),
                        TimePicker::make('time_in')->seconds(false),
                        TimePicker::make('time_out')->seconds(false),
                        TextInput::make('source')->default('manual')->disabled(),
                        TextInput::make('late_minutes')->label('Late (Minutes)')->numeric()->default(0),
                        TextInput::make('undertime_minutes')->label('Undertime (Minutes)')->numeric()->default(0),
                        TextInput::make('overtime_hours')->label('Overtime (Hours)')->numeric()->default(0),
                        Toggle::make('is_holiday')->label('Holiday Worked'),
                        Textarea::make('remarks')->columnSpanFull()->rows(2),
                    ]),
            ]);
    }
}
