<?php

namespace App\Filament\Resources\EvaluationPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EvaluationPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Evaluation Cycle Details')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('name')
                            ->label('Cycle Name')
                            ->placeholder('e.g. Q1 2026 Group Performance Evaluation')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('period_type')
                            ->label('Cadence')
                            ->options([
                                'monthly' => 'Monthly Review',
                                'quarterly' => 'Quarterly Cycle',
                                'yearly' => 'Annual Performance Appraisal',
                            ])
                            ->default('quarterly')
                            ->required(),
                        Select::make('entity_id')
                            ->label('Target Entity')
                            ->relationship('entity', 'name')
                            ->placeholder('All KM100 Entities (Group-wide)')
                            ->helperText('Leave empty for group-wide ranking cycle.'),
                        DatePicker::make('start_date')
                            ->label('Period Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Period End Date')
                            ->required()
                            ->afterOrEqual('start_date'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Active' => 'Active',
                                'Closed' => 'Closed (Computing)',
                                'Finalized' => 'Finalized & Locked',
                            ])
                            ->default('Active')
                            ->required(),
                    ]),
                ]),

            Section::make('Top Performers Ranking Factor Weights')
                ->description('Configure the relative weight percentages for the 5 ranking components. Default: Evaluation 50%, Attendance 20%, Training 15%, Discipline 10%, Commendations 5%.')
                ->schema([
                    Grid::make(5)->schema([
                        TextInput::make('weight_evaluation')
                            ->label('Evaluation Score (%)')
                            ->numeric()
                            ->default(50)
                            ->required()
                            ->suffix('%'),
                        TextInput::make('weight_attendance')
                            ->label('Attendance & Punctuality (%)')
                            ->numeric()
                            ->default(20)
                            ->required()
                            ->suffix('%'),
                        TextInput::make('weight_training')
                            ->label('Training & Certifications (%)')
                            ->numeric()
                            ->default(15)
                            ->required()
                            ->suffix('%'),
                        TextInput::make('weight_discipline')
                            ->label('Discipline & Conduct (%)')
                            ->numeric()
                            ->default(10)
                            ->required()
                            ->suffix('%'),
                        TextInput::make('weight_commendations')
                            ->label('Commendations (%)')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->suffix('%'),
                    ]),
                ]),
        ]);
    }
}
