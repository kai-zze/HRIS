<?php

namespace App\Filament\Employee\Resources\MyOvertimeRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MyOvertimeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Overtime Application')
                ->description('File advance authorization for required overtime work.')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('overtime_date')
                            ->label('OT Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->default(today())
                            ->required(),
                        TimePicker::make('start_time')
                            ->label('Expected Start Time')
                            ->seconds(false)
                            ->default('17:00')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Expected End Time')
                            ->seconds(false)
                            ->default('20:00')
                            ->required(),
                    ]),
                    Textarea::make('purpose')
                        ->label('Reason / Business Urgency')
                        ->rows(3)
                        ->required()
                        ->placeholder('Explain the specific task, client deliverable, or operational requirement.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
