<?php

namespace App\Filament\Employee\Resources\MyLeaveRequests\Schemas;

use App\Models\Employee;
use App\Models\LeaveType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MyLeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        return $schema->components([
            Section::make('File Leave Request')
                ->description('Submit a formal leave application for supervisor endorsement and HR approval.')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('leave_type_id')
                            ->label('Leave Type')
                            ->options(function () use ($employee) {
                                return LeaveType::withoutGlobalScopes()
                                    ->where('is_active', true)
                                    ->where(function ($q) use ($employee) {
                                        $q->whereNull('entity_id');
                                        if ($employee) {
                                            $q->orWhere('entity_id', $employee->entity_id);
                                        }
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->columnSpan(2),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->required()
                            ->live(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->required()
                            ->afterOrEqual('start_date')
                            ->live(),
                        Toggle::make('is_half_day')
                            ->label('Half-day Leave')
                            ->live()
                            ->columnSpanFull(),
                        Radio::make('half_day_type')
                            ->label('Half-day Session')
                            ->options([
                                'morning' => 'Morning (AM)',
                                'afternoon' => 'Afternoon (PM)',
                            ])
                            ->visible(fn ($get) => (bool) $get('is_half_day'))
                            ->default('morning')
                            ->inline()
                            ->columnSpanFull(),
                    ]),
                    Textarea::make('reason')
                        ->label('Reason / Justification')
                        ->rows(3)
                        ->required()
                        ->placeholder('State the detailed reason for this leave application.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
