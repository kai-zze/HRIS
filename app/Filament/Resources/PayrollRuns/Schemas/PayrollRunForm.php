<?php

namespace App\Filament\Resources\PayrollRuns\Schemas;

use App\Models\Entity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayrollRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payroll Run Details')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('entity_id')
                            ->label('Entity')
                            ->relationship('entity', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->label('Run Name / Description')
                            ->placeholder('e.g. January 1-15, 2026 Regular Cutoff')
                            ->required()
                            ->columnSpan(2),
                        DatePicker::make('cutoff_start')
                            ->label('Cutoff Start Date')
                            ->required(),
                        DatePicker::make('cutoff_end')
                            ->label('Cutoff End Date')
                            ->required(),
                        DatePicker::make('payout_date')
                            ->label('Payout Date')
                            ->required(),
                        Select::make('period_type')
                            ->label('Period Type')
                            ->options([
                                'first_half' => '1st Half (1st-15th)',
                                'second_half' => '2nd Half (16th-End)',
                                'thirteenth_month' => '13th Month Pay',
                                'special' => 'Special / Bonus',
                            ])
                            ->default('first_half')
                            ->required(),
                        Select::make('status')
                            ->label('Workflow Status')
                            ->options([
                                'draft' => 'Draft',
                                'review' => 'Under Review',
                                'approved' => 'Approved',
                                'locked' => 'Locked',
                            ])
                            ->default('draft')
                            ->required(),
                        TextInput::make('year')
                            ->label('Calendar Year')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),
                    Textarea::make('notes')
                        ->label('Internal Notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
