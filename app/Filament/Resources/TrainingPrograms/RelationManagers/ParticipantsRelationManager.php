<?php

namespace App\Filament\Resources\TrainingPrograms\RelationManagers;

use App\Models\Employee;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $recordTitleAttribute = 'certificate_number';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn () => Employee::withoutGlobalScopes()->get()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Nominated' => 'Nominated',
                        'Attended' => 'Attended',
                        'Completed' => 'Completed & Certified',
                        'Failed' => 'Failed',
                        'No Show' => 'No Show',
                    ])
                    ->default('Completed')
                    ->required(),
                TextInput::make('certificate_number')
                    ->label('Certificate Number')
                    ->placeholder('e.g. KM100-CERT-2026-0819'),
                TextInput::make('score_rating')
                    ->label('Assessment Score (%)')
                    ->numeric()
                    ->maxValue(100),
                DatePicker::make('certificate_issued_at')
                    ->label('Date Issued')
                    ->default(today()),
                DatePicker::make('certificate_expiry_date')
                    ->label('Expiry Date (if renewing)')
                    ->helperText('Track re-certification validity for TESDA or OSH licenses.'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Participant')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Completed' => 'success',
                        'Attended' => 'info',
                        'Nominated' => 'warning',
                        'Failed', 'No Show' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('certificate_number')
                    ->label('Certificate #')
                    ->placeholder('—'),
                TextColumn::make('score_rating')
                    ->label('Score')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '—')
                    ->alignCenter(),
                TextColumn::make('certificate_expiry_date')
                    ->label('Valid Until')
                    ->date('M d, Y')
                    ->placeholder('Permanent')
                    ->color(fn ($state) => $state && now()->diffInDays($state, false) <= 60 ? 'danger' : 'gray'),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Participant'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
