<?php

namespace App\Filament\Resources\JobApplicants\Tables;

use App\Enums\ApplicantStage;
use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\OnboardingChecklist;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Applicant')
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),
                TextColumn::make('jobPosting.title')
                    ->label('Role')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                TextColumn::make('entity.short_name')
                    ->label('Entity'),
                TextColumn::make('contact_number')
                    ->label('Contact')
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label('Score')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state) : '—')
                    ->color('warning')
                    ->alignCenter(),
                TextColumn::make('stage')
                    ->color(fn (ApplicantStage $state): string => $state->getColor()),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('stage')
                    ->options(ApplicantStage::class),
                SelectFilter::make('job_posting_id')
                    ->relationship('jobPosting', 'title')
                    ->label('Job Vacancy'),
                SelectFilter::make('entity_id')
                    ->relationship('entity', 'name')
                    ->label('Entity'),
            ])
            ->actions([
                // Quick Stage Advancement Action
                Action::make('updateStage')
                    ->label('Advance Stage')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('info')
                    ->form([
                        Select::make('stage')
                            ->label('New Pipeline Stage')
                            ->options(ApplicantStage::class)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['stage' => $data['stage']]);
                        Notification::make()->title('Applicant Stage Updated')->success()->send();
                    }),

                // Convert Hired Applicant to Employee Record
                Action::make('convertToEmployee')
                    ->label('Convert to Employee')
                    ->icon('heroicon-m-user-plus')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->hired_employee_id)
                    ->form([
                        TextInput::make('basic_salary')
                            ->label('Starting Basic Monthly Salary')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->default(fn ($record) => $record->expected_salary ?? 25000),
                        DatePicker::make('date_hired')
                            ->label('Official Date Hired')
                            ->default(today())
                            ->required(),
                        Select::make('employment_status')
                            ->label('Initial Employment Status')
                            ->options(EmploymentStatus::class)
                            ->default(EmploymentStatus::Probationary)
                            ->required(),
                        Select::make('supervisor_id')
                            ->label('Immediate Supervisor')
                            ->options(fn ($record) => Employee::withoutGlobalScopes()
                                ->where('entity_id', $record->entity_id)
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable(),
                    ])
                    ->action(function ($record, array $data) {
                        $entity = $record->entity;
                        $count = Employee::withoutGlobalScopes()->where('entity_id', $entity->id)->count() + 1;
                        $empNumber = sprintf('%s-2026-%04d', $entity->code, $count);

                        $employee = Employee::withoutGlobalScopes()->create([
                            'entity_id' => $record->entity_id,
                            'department_id' => $record->jobPosting?->department_id,
                            'position_id' => $record->jobPosting?->position_id,
                            'employee_number' => $empNumber,
                            'first_name' => $record->first_name,
                            'middle_name' => $record->middle_name,
                            'last_name' => $record->last_name,
                            'email' => $record->email,
                            'contact_number' => $record->contact_number,
                            'birthdate' => '1995-01-01', // Placeholder until 201 update
                            'sex' => 'male',
                            'civil_status' => 'single',
                            'residential_address' => 'Philippines',
                            'permanent_address' => 'Philippines',
                            'employment_status' => $data['employment_status'],
                            'basic_salary' => $data['basic_salary'],
                            'date_hired' => $data['date_hired'],
                            'supervisor_id' => $data['supervisor_id'] ?? null,
                        ]);

                        $record->update([
                            'stage' => ApplicantStage::Hired,
                            'hired_employee_id' => $employee->id,
                            'hired_at' => now(),
                        ]);

                        // Seed default onboarding checklist items
                        $checklistItems = [
                            ['item_name' => 'Submit NBI & Police Clearances', 'category' => 'Document Submission'],
                            ['item_name' => 'Submit PSA Birth & Marriage Certificates', 'category' => 'Document Submission'],
                            ['item_name' => 'Provide Form 2316 / Prior Compensation Certificate', 'category' => 'Statutory'],
                            ['item_name' => 'Register Biometrics Profile in Timekeeper Terminals', 'category' => 'Orientation'],
                            ['item_name' => 'Issue Corporate Email & HRIS Portal Access', 'category' => 'IT & Equipment'],
                            ['item_name' => 'Conduct KM100 Company Orientation & Handbook Review', 'category' => 'Orientation'],
                        ];

                        foreach ($checklistItems as $item) {
                            OnboardingChecklist::create([
                                'employee_id' => $employee->id,
                                'item_name' => $item['item_name'],
                                'category' => $item['category'],
                                'is_completed' => false,
                            ]);
                        }

                        Notification::make()
                            ->title('Employee Record Created')
                            ->body("Successfully generated 201 file for {$employee->first_name} {$employee->last_name} ({$empNumber}) with onboarding checklist.")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
