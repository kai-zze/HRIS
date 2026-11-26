<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Enums\RequestStatus;
use App\Models\Employee;
use App\Models\LeaveType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Leave Application')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn () => Employee::all()->pluck('full_name', 'id'))
                            ->required()
                            ->searchable(),
                        Select::make('leave_type_id')
                            ->label('Leave Type')
                            ->options(fn () => LeaveType::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        Select::make('status')
                            ->options(RequestStatus::class)
                            ->default(RequestStatus::Pending)
                            ->required(),
                        DatePicker::make('start_date')->required()->native(false),
                        DatePicker::make('end_date')->required()->native(false),
                        TextInput::make('number_of_days')->numeric()->default(1)->required(),
                        Toggle::make('is_half_day')->label('Half Day Only'),
                        FileUpload::make('attachment_path')
                            ->label('Medical Certificate / Attachment')
                            ->directory('leave-attachments')
                            ->columnSpan(2),
                        Textarea::make('reason')->columnSpanFull()->required()->rows(2),
                        Textarea::make('manager_notes')->label('Supervisor Notes')->columnSpan(1)->rows(2),
                        Textarea::make('hr_notes')->label('HR Notes')->columnSpan(2)->rows(2),
                    ]),
            ]);
    }
}
