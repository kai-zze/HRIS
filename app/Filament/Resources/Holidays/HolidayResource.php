<?php

namespace App\Filament\Resources\Holidays;

use App\Enums\HolidayType;
use App\Filament\Resources\Holidays\Pages\CreateHoliday;
use App\Filament\Resources\Holidays\Pages\EditHoliday;
use App\Filament\Resources\Holidays\Pages\ListHolidays;
use App\Models\Entity;
use App\Models\Holiday;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Time & Attendance';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Philippine Holiday Setup')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')->required()->maxLength(150),
                        DatePicker::make('holiday_date')->required()->native(false),
                        Select::make('holiday_type')
                            ->options(HolidayType::class)
                            ->required(),
                        Select::make('entity_id')
                            ->label('Entity (Leave blank for Nationwide)')
                            ->options(fn () => Entity::pluck('short_name', 'id'))
                            ->placeholder('Nationwide (All Entities)')
                            ->searchable(),
                        Textarea::make('description')->columnSpan(2)->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('holiday_date')->label('Date')->date('F d, Y (l)')->sortable()->weight('bold'),
                TextColumn::make('name')->label('Holiday Name')->searchable()->sortable(),
                TextColumn::make('holiday_type')->label('Type')->badge(),
                TextColumn::make('entity.short_name')->label('Applicable Entity')->placeholder('Nationwide')->badge()->color('gray'),
            ])
            ->defaultSort('holiday_date', 'asc')
            ->filters([
                SelectFilter::make('holiday_type')->options(HolidayType::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHolidays::route('/'),
            'create' => CreateHoliday::route('/create'),
            'edit' => EditHoliday::route('/{record}/edit'),
        ];
    }
}
