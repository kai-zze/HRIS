<?php

namespace App\Filament\Resources\DisciplinaryRecords\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DisciplinaryRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Disciplinary Incident & Notice to Explain')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn () => Employee::withoutGlobalScopes()->get()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($emp = Employee::withoutGlobalScopes()->find($state)) {
                                    $set('entity_id', $emp->entity_id);
                                }
                            })
                            ->columnSpan(2),
                        Select::make('entity_id')
                            ->label('Entity')
                            ->relationship('entity', 'name')
                            ->required(),
                        TextInput::make('case_number')
                            ->label('Case / Docket Number')
                            ->default(fn () => 'DISC-' . date('Y') . '-' . sprintf('%04d', rand(100, 999)))
                            ->required(),
                        DatePicker::make('incident_date')
                            ->label('Date of Incident')
                            ->required(),
                        Select::make('violation_category')
                            ->label('Violation Category')
                            ->options([
                                'Tardiness & Habitual Absenteeism' => 'Tardiness & Habitual Absenteeism',
                                'Gross Misconduct / Insubordination' => 'Gross Misconduct / Insubordination',
                                'Negligence of Duty' => 'Negligence of Duty',
                                'Breach of Company Code of Conduct' => 'Breach of Company Code of Conduct',
                                'Dishonesty / False Statements' => 'Dishonesty / False Statements',
                            ])
                            ->required(),
                        Select::make('status')
                            ->label('Status of Due Process')
                            ->options([
                                'Incident Reported' => 'Incident Reported',
                                'NTE Issued' => 'Notice to Explain (NTE) Issued',
                                'Hearing' => 'Administrative Hearing / Inquiry',
                                'Sanction Imposed' => 'Decision Rendered / Sanction Imposed',
                                'Case Closed' => 'Case Closed / Exonerated',
                            ])
                            ->default('Incident Reported')
                            ->required(),
                        Select::make('sanction')
                            ->label('Imposed Sanction')
                            ->options([
                                'Verbal Warning' => 'Verbal Warning (Documented)',
                                'Written Warning' => 'Written Warning (First Offense)',
                                'Written Reprimand' => 'Written Reprimand (Second Offense)',
                                'Suspension' => 'Suspension from Work',
                                'Termination' => 'Termination for Just Cause',
                                'Exonerated' => 'Exonerated / Cleared of Charges',
                            ]),
                        TextInput::make('suspension_days')
                            ->label('Days of Suspension (if applicable)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30),
                        TextInput::make('penalty_points')
                            ->label('Ranking Penalty Deduction')
                            ->numeric()
                            ->default(10)
                            ->helperText('Deducted from the employee’s 10% discipline score.')
                            ->required(),
                    ]),
                    Textarea::make('description')
                        ->label('Incident Description & Alleged Facts')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('employee_explanation')
                        ->label('Employee Written Answer / Explanation')
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('hr_findings')
                        ->label('HR Legal / Management Findings & Disposition')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
