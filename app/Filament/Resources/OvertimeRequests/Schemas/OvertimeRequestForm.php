<?php

namespace App\Filament\Resources\OvertimeRequests\Schemas;

use App\Enums\RequestStatus;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OvertimeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overtime Request Details')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn () => Employee::all()->pluck('full_name', 'id'))
                            ->required()
                            ->searchable(),
                        DatePicker::make('overtime_date')
                            ->label('Render Date')
                            ->required()
                            ->native(false),
                        TextInput::make('hours_requested')
                            ->label('Hours')
                            ->numeric()
                            ->required(),
                        TimePicker::make('start_time')->seconds(false)->required(),
                        TimePicker::make('end_time')->seconds(false)->required(),
                        Select::make('status')
                            ->options(RequestStatus::class)
                            ->default(RequestStatus::Pending)
                            ->required(),
                        Textarea::make('purpose')->columnSpanFull()->required()->rows(2),
                        Textarea::make('manager_notes')->label('Supervisor Notes')->columnSpan(1)->rows(2),
                        Textarea::make('hr_notes')->label('HR Notes')->columnSpan(2)->rows(2),
                    ]),
            ]);
    }
}
