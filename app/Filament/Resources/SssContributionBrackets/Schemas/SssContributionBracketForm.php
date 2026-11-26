<?php

namespace App\Filament\Resources\SssContributionBrackets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SssContributionBracketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('SSS Bracket & Share Specification')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('range_min')
                            ->label('Range Min (₱)')
                            ->numeric()
                            ->required(),
                        TextInput::make('range_max')
                            ->label('Range Max (₱)')
                            ->numeric()
                            ->placeholder('Leave empty if open-ended (top bracket)'),
                        TextInput::make('monthly_salary_credit')
                            ->label('Monthly Salary Credit (MSC ₱)')
                            ->numeric()
                            ->required(),
                        TextInput::make('regular_employee_share')
                            ->label('Regular EE Share (₱)')
                            ->numeric()
                            ->required(),
                        TextInput::make('regular_employer_share')
                            ->label('Regular ER Share (₱)')
                            ->numeric()
                            ->required(),
                        TextInput::make('ec_employer_share')
                            ->label('EC ER Share (₱)')
                            ->numeric()
                            ->default(10.00)
                            ->required(),
                        TextInput::make('wisp_employee_share')
                            ->label('WISP / MPF EE Share (₱)')
                            ->numeric()
                            ->default(0.00),
                        TextInput::make('wisp_employer_share')
                            ->label('WISP / MPF ER Share (₱)')
                            ->numeric()
                            ->default(0.00),
                        TextInput::make('effective_year')
                            ->label('Effective Year')
                            ->numeric()
                            ->default(2026)
                            ->required(),
                    ]),
                    Toggle::make('is_active')
                        ->label('Active Bracket')
                        ->default(true),
                ]),
        ]);
    }
}
