<?php

namespace App\Filament\Resources\JobApplicants\Schemas;

use App\Enums\ApplicantStage;
use App\Models\JobPosting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Applicant Personal & Application Details')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('job_posting_id')
                            ->label('Job Vacancy Applied For')
                            ->relationship('jobPosting', 'title')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($posting = JobPosting::find($state)) {
                                    $set('entity_id', $posting->entity_id);
                                }
                            })
                            ->columnSpan(2),
                        Select::make('entity_id')
                            ->label('Entity')
                            ->relationship('entity', 'name')
                            ->required(),
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(150),
                        TextInput::make('contact_number')
                            ->label('Mobile / Contact Number')
                            ->tel()
                            ->required()
                            ->placeholder('+63 917 123 4567'),
                        TextInput::make('expected_salary')
                            ->label('Expected Monthly Salary')
                            ->numeric()
                            ->prefix('₱')
                            ->placeholder('30000.00'),
                        Select::make('stage')
                            ->label('Recruitment Stage')
                            ->options(ApplicantStage::class)
                            ->default(ApplicantStage::Applied)
                            ->required(),
                        Select::make('rating')
                            ->label('Interview / Candidate Rating')
                            ->options([
                                1 => '⭐ (Poor fit)',
                                2 => '⭐⭐ (Below Average)',
                                3 => '⭐⭐⭐ (Qualified / Good)',
                                4 => '⭐⭐⭐⭐ (Very Strong)',
                                5 => '⭐⭐⭐⭐⭐ (Exceptional Fit)',
                            ]),
                        FileUpload::make('resume_path')
                            ->label('Resume / Curriculum Vitae (PDF)')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->directory('resumes'),
                    ]),
                    Textarea::make('interview_notes')
                        ->label('Interview Notes & Evaluator Feedback')
                        ->rows(4)
                        ->placeholder('Summary of technical assessment, behavioral screening, culture fit, and compensation discussion.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
