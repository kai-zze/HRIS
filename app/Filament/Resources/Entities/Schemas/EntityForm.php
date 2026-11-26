<?php

namespace App\Filament\Resources\Entities\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EntityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entity Identity')
                    ->description('Registered company profile within the KM100 corporate group.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Entity Code')
                            ->required()
                            ->maxLength(32)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. KM100-STC')
                            ->extraInputAttributes(['class' => 'font-mono uppercase']),
                        TextInput::make('short_name')
                            ->label('Short Name')
                            ->required()
                            ->maxLength(60)
                            ->placeholder('e.g. KM100 STC'),
                        TextInput::make('name')
                            ->label('Registered Legal Name')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull()
                            ->placeholder('e.g. KM100 Skills and Training Center, Inc.'),
                        Toggle::make('is_active')
                            ->label('Active Entity')
                            ->default(true)
                            ->helperText('Inactive entities remain in historical records but cannot receive new employees.'),
                    ]),

                Section::make('Contact & Registered Address')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Official HR Email')
                            ->email()
                            ->maxLength(150)
                            ->placeholder('hr@km100group.ph'),
                        TextInput::make('contact_number')
                            ->label('Contact Number')
                            ->tel()
                            ->maxLength(50)
                            ->placeholder('+63 2 8000 1000'),
                        Textarea::make('address')
                            ->label('Registered Business Address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Statutory Employer Numbers (RA 10173 Encrypted)')
                    ->description('Stored using AES-256 encrypted casts. Masked in directory tables.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tin')
                            ->label('BIR TIN')
                            ->maxLength(30)
                            ->placeholder('000-000-000-000')
                            ->extraInputAttributes(['class' => 'font-mono']),
                        TextInput::make('sss_employer_number')
                            ->label('SSS Employer Number')
                            ->maxLength(30)
                            ->extraInputAttributes(['class' => 'font-mono']),
                        TextInput::make('philhealth_employer_number')
                            ->label('PhilHealth Employer Number')
                            ->maxLength(30)
                            ->extraInputAttributes(['class' => 'font-mono']),
                        TextInput::make('pagibig_employer_number')
                            ->label('Pag-IBIG Employer Number')
                            ->maxLength(30)
                            ->extraInputAttributes(['class' => 'font-mono']),
                    ]),
            ]);
    }
}
