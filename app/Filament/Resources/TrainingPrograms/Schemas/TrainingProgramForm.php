<?php

namespace App\Filament\Resources\TrainingPrograms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Training Program Curriculum & Provider')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('title')
                            ->label('Program Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('entity_id')
                            ->label('Target Entity')
                            ->relationship('entity', 'name')
                            ->placeholder('Group-wide (All Entities)')
                            ->helperText('Leave empty for group-wide seminars.'),
                        Select::make('category')
                            ->label('Category')
                            ->options([
                                'Technical' => 'Technical & Competency Training',
                                'Safety' => 'Occupational Safety & Health (OSH)',
                                'Compliance' => 'Statutory & Regulatory Compliance',
                                'Leadership' => 'Supervisory & Leadership Development',
                                'Soft Skills' => 'Customer Service & Soft Skills',
                            ])
                            ->default('Technical')
                            ->required(),
                        Select::make('provider')
                            ->label('Training Provider')
                            ->options([
                                'Internal KM100 Academy' => 'Internal KM100 Training Center',
                                'TESDA Accredited' => 'TESDA Registered / Accredited',
                                'DOLE / OSHC Accredited' => 'DOLE / OSHC Safety Center',
                                'External Specialized Partner' => 'External Professional Partner',
                            ])
                            ->default('Internal KM100 Academy')
                            ->required(),
                        TextInput::make('duration_hours')
                            ->label('Duration (Hours)')
                            ->numeric()
                            ->default(8.0)
                            ->minValue(1)
                            ->required(),
                        TextInput::make('trainer_name')
                            ->label('Lead Trainer / Facilitator')
                            ->maxLength(150),
                        TextInput::make('trainer_credentials')
                            ->label('Trainer Accreditation / License')
                            ->placeholder('e.g. TESDA NTTC No. 129034 / PRC License 049182')
                            ->maxLength(200),
                        TextInput::make('location')
                            ->label('Venue / Training Room')
                            ->default('KM100 Training Facility A')
                            ->maxLength(150),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->afterOrEqual('start_date'),
                    ]),
                    Textarea::make('description')
                        ->label('Syllabus & Learning Outcomes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
