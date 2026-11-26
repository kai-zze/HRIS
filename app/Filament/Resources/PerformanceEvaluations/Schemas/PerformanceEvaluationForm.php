<?php

namespace App\Filament\Resources\PerformanceEvaluations\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PerformanceEvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Appraisal Form & Target')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('evaluation_period_id')
                            ->label('Evaluation Period')
                            ->relationship('evaluationPeriod', 'name')
                            ->required(),
                        Select::make('employee_id')
                            ->label('Ratee / Employee')
                            ->options(fn () => Employee::withoutGlobalScopes()->get()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('evaluation_type')
                            ->label('Review Type')
                            ->options([
                                'regular_kpi' => 'Regular Periodic KPI Review',
                                'probation_review' => '5th Month Regularization / Probation Review',
                            ])
                            ->default('regular_kpi')
                            ->required(),
                        Select::make('evaluator_id')
                            ->label('Evaluator / Supervisor')
                            ->relationship('evaluator', 'name')
                            ->default(auth()->id())
                            ->required(),
                        Select::make('status')
                            ->label('Appraisal Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Submitted' => 'Submitted for HR Audit',
                                'Approved' => 'Approved & Finalized',
                            ])
                            ->default('Submitted')
                            ->required(),
                        TextInput::make('score')
                            ->label('Overall Average Score (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->suffix('pts'),
                    ]),
                ]),

            Section::make('Key Performance Indicators (KPIs) & Criteria')
                ->schema([
                    Repeater::make('criteria')
                        ->relationship('criteria')
                        ->schema([
                            TextInput::make('criterion_name')
                                ->label('Performance Competency / Metric')
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('weight_percentage')
                                ->label('Weight')
                                ->numeric()
                                ->default(25)
                                ->suffix('%'),
                            TextInput::make('score')
                                ->label('Score (0-100)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->default(85),
                            TextInput::make('remarks')
                                ->label('Remarks / Observable Behavior')
                                ->columnSpanFull(),
                        ])
                        ->columns(4)
                        ->defaultItems(4)
                        ->addActionLabel('Add Evaluation Metric'),
                ]),

            Section::make('Qualitative Feedback')
                ->schema([
                    Grid::make(2)->schema([
                        Textarea::make('strengths')
                            ->label('Key Strengths & Notable Accomplishments')
                            ->rows(3),
                        Textarea::make('improvements')
                            ->label('Areas for Growth & Action Plan')
                            ->rows(3),
                        Textarea::make('supervisor_comments')
                            ->label('Supervisor Summary Recommendation')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
                ]),
        ]);
    }
}
