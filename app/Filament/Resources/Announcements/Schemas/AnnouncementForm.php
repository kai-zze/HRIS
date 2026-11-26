<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Entity;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Announcement Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->label('Title / Headline')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('entity_id')
                            ->label('Target Entity')
                            ->relationship('entity', 'name')
                            ->placeholder('All Entities (Group-wide Announcement)')
                            ->helperText('Leave empty to broadcast to all KM100 group companies.'),
                        Select::make('category')
                            ->label('Category')
                            ->options([
                                'General' => 'General Notice',
                                'Policy' => 'Company Policy Update',
                                'Holiday' => 'Holiday & Schedule Advisory',
                                'Event' => 'Company Event / Townhall',
                                'Urgent' => 'Urgent Advisory',
                            ])
                            ->default('General')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Publish Date & Time')
                            ->default(now()),
                        Toggle::make('is_pinned')
                            ->label('Pin to Top of Portal')
                            ->helperText('Pinned announcements appear prominently on the employee dashboard.')
                            ->default(false),
                    ]),
                    RichEditor::make('content')
                        ->label('Announcement Message')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
