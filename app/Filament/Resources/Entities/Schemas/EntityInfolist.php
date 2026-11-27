<?php

namespace App\Filament\Resources\Entities\Schemas;

use App\Models\Entity;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EntityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entity Overview')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('logo_url')
                            ->label('Company Logo')
                            ->getStateUsing(fn (Entity $record): string => $record->logo_url)
                            ->imageHeight(100)
                            ->extraImgAttributes([
                                'class' => 'object-contain max-w-xs',
                                'style' => 'border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; object-fit: contain;',
                            ])
                            ->columnSpanFull(),
                        TextEntry::make('code')
                            ->label('Entity Code')
                            ->color('primary'),
                        TextEntry::make('short_name')
                            ->label('Short Name'),
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                        TextEntry::make('name')
                            ->label('Registered Legal Name')
                            ->columnSpan(2),
                        TextEntry::make('email')
                            ->label('Official HR Email')
                            ->placeholder('—'),
                        TextEntry::make('contact_number')
                            ->label('Contact Number')
                            ->placeholder('—'),
                        TextEntry::make('address')
                            ->label('Registered Business Address')
                            ->columnSpan(2)
                            ->placeholder('—'),
                    ]),

                Section::make('Statutory Registration (Masked)')
                    ->description('Masked per RA 10173 Data Privacy Act. Authorized admins can edit in Update view.')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('tin')
                            ->label('BIR TIN')
                            ->state(fn (Entity $record): string => Entity::maskIdentifier($record->tin)),
                        TextEntry::make('sss_employer_number')
                            ->label('SSS Employer')
                            ->state(fn (Entity $record): string => Entity::maskIdentifier($record->sss_employer_number)),
                        TextEntry::make('philhealth_employer_number')
                            ->label('PhilHealth Employer')
                            ->state(fn (Entity $record): string => Entity::maskIdentifier($record->philhealth_employer_number)),
                        TextEntry::make('pagibig_employer_number')
                            ->label('Pag-IBIG Employer')
                            ->state(fn (Entity $record): string => Entity::maskIdentifier($record->pagibig_employer_number)),
                    ]),
            ]);
    }
}
