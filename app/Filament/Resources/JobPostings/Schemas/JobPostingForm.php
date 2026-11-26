<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use App\Models\Department;
use App\Models\Entity;
use App\Models\Position;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobPostingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Opening Information')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('entity_id')
                            ->label('Hiring Entity')
                            ->relationship('entity', 'name')
                            ->required()
                            ->live(),
                        Select::make('department_id')
                            ->label('Department')
                            ->options(function ($get) {
                                $entityId = $get('entity_id');
                                if (! $entityId) return [];
                                return Department::withoutEntityScope()->where('entity_id', $entityId)->pluck('name', 'id');
                            })
                            ->required(),
                        Select::make('position_id')
                            ->label('Position / Role')
                            ->options(function ($get) {
                                $entityId = $get('entity_id');
                                if (! $entityId) return [];
                                return Position::withoutEntityScope()->where('entity_id', $entityId)->pluck('title', 'id');
                            })
                            ->required(),
                        TextInput::make('title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('employment_type')
                            ->label('Employment Type')
                            ->options([
                                'Full-Time' => 'Full-Time Regular',
                                'Probationary' => 'Probationary (6 Months)',
                                'Contractual' => 'Project-Based / Contractual',
                                'Part-Time' => 'Part-Time',
                            ])
                            ->default('Full-Time')
                            ->required(),
                        TextInput::make('openings_count')
                            ->label('Number of Openings')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        TextInput::make('salary_range')
                            ->label('Salary Budget / Range')
                            ->placeholder('e.g. ₱25,000 — ₱35,000 / month')
                            ->maxLength(100),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Open' => 'Open & Accepting Applicants',
                                'On Hold' => 'On Hold',
                                'Closed' => 'Closed / Position Filled',
                            ])
                            ->default('Open')
                            ->required(),
                        DatePicker::make('posted_at')
                            ->label('Date Posted')
                            ->default(today()),
                        DatePicker::make('closing_date')
                            ->label('Application Deadline')
                            ->afterOrEqual('posted_at'),
                    ]),
                    RichEditor::make('description')
                        ->label('Job Summary & Key Responsibilities')
                        ->required()
                        ->columnSpanFull(),
                    RichEditor::make('requirements')
                        ->label('Qualifications & Requirements')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
