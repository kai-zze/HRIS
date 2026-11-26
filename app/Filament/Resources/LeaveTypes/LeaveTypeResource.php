<?php

namespace App\Filament\Resources\LeaveTypes;

use App\Filament\Resources\LeaveTypes\Pages\CreateLeaveType;
use App\Filament\Resources\LeaveTypes\Pages\EditLeaveType;
use App\Filament\Resources\LeaveTypes\Pages\ListLeaveTypes;
use App\Models\Entity;
use App\Models\LeaveType;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Leave Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Leave Policy Configuration')
                    ->columns(3)
                    ->schema([
                        TextInput::make('code')->required()->extraInputAttributes(['class' => 'font-mono uppercase']),
                        TextInput::make('name')->required()->columnSpan(2),
                        Select::make('entity_id')
                            ->label('Entity (Leave blank for Group Standard)')
                            ->options(fn () => Entity::pluck('short_name', 'id'))
                            ->placeholder('All KM100 Entities'),
                        TextInput::make('default_credit_days')->numeric()->default(15)->required(),
                        Toggle::make('is_paid')->label('Paid Leave')->default(true),
                        Toggle::make('is_convertible_to_cash')->label('Convertible to Cash'),
                        Toggle::make('requires_medical_certificate')->label('Requires Medical Certificate'),
                        Toggle::make('is_active')->label('Active Policy')->default(true),
                        Textarea::make('description')->columnSpanFull()->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Code')->fontFamily('mono'),
                TextColumn::make('name')->label('Leave Type')->searchable()->weight('bold'),
                TextColumn::make('default_credit_days')->label('Credits (Days)')->alignEnd(),
                IconColumn::make('is_paid')->label('Paid')->boolean(),
                IconColumn::make('is_convertible_to_cash')->label('Cashable')->boolean(),
                IconColumn::make('requires_medical_certificate')->label('Med Cert')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'edit' => EditLeaveType::route('/{record}/edit'),
        ];
    }
}
