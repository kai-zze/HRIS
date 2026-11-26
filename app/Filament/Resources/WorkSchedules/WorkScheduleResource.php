<?php

namespace App\Filament\Resources\WorkSchedules;

use App\Filament\Resources\WorkSchedules\Pages\CreateWorkSchedule;
use App\Filament\Resources\WorkSchedules\Pages\EditWorkSchedule;
use App\Filament\Resources\WorkSchedules\Pages\ListWorkSchedules;
use App\Models\Entity;
use App\Models\WorkSchedule;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class WorkScheduleResource extends Resource
{
    protected static ?string $model = WorkSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Time & Attendance';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Work Schedule / Shift Setup')
                    ->columns(3)
                    ->schema([
                        Select::make('entity_id')
                            ->label('Entity')
                            ->options(fn () => Entity::pluck('short_name', 'id'))
                            ->required(),
                        TextInput::make('name')->required()->placeholder('Regular Day Shift')->columnSpan(2),
                        TimePicker::make('time_in')->seconds(false)->required()->default('08:00:00'),
                        TimePicker::make('time_out')->seconds(false)->required()->default('17:00:00'),
                        TextInput::make('break_minutes')->numeric()->default(60)->required(),
                        TextInput::make('grace_period_minutes')->numeric()->default(15)->required(),
                        Toggle::make('is_default')->label('Default Entity Schedule')->default(false),
                        Toggle::make('is_active')->label('Active Schedule')->default(true),
                        CheckboxList::make('working_days')
                            ->label('Working Days')
                            ->options([
                                'mon' => 'Monday',
                                'tue' => 'Tuesday',
                                'wed' => 'Wednesday',
                                'thu' => 'Thursday',
                                'fri' => 'Friday',
                                'sat' => 'Saturday',
                                'sun' => 'Sunday',
                            ])
                            ->default(['mon', 'tue', 'wed', 'thu', 'fri'])
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Schedule Name')->searchable()->weight('bold'),
                TextColumn::make('entity.short_name')->label('Entity')->sortable(),
                TextColumn::make('time_in')->time('g:i A'),
                TextColumn::make('time_out')->time('g:i A'),
                TextColumn::make('grace_period_minutes')->label('Grace')->formatStateUsing(fn ($state) => "{$state}m"),
                IconColumn::make('is_default')->label('Default')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkSchedules::route('/'),
            'create' => CreateWorkSchedule::route('/create'),
            'edit' => EditWorkSchedule::route('/{record}/edit'),
        ];
    }
}
