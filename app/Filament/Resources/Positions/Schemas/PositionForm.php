<?php

namespace App\Filament\Resources\Positions\Schemas;

use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Services\EntityContextService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Position & Job Title')
                    ->columns(2)
                    ->schema([
                        Select::make('entity_id')
                            ->label('Entity')
                            ->options(fn (): array => Entity::query()
                                ->where('is_active', true)
                                ->orderBy('short_name')
                                ->pluck('short_name', 'id')
                                ->all())
                            ->default(function (): ?int {
                                $user = Auth::user();
                                if ($user instanceof User && ! $user->isSuperAdmin()) {
                                    return $user->entity_id;
                                }

                                return app(EntityContextService::class)->activeEntityId();
                            })
                            ->disabled(fn (): bool => Auth::user() instanceof User && ! Auth::user()->isSuperAdmin())
                            ->dehydrated()
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('department_id', null)),
                        Select::make('department_id')
                            ->label('Department')
                            ->options(fn (Get $get): array => Department::withoutEntityScope()
                                ->where('entity_id', $get('entity_id'))
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->placeholder('Select department (optional)'),
                        TextInput::make('code')
                            ->label('Position Code')
                            ->required()
                            ->maxLength(32)
                            ->placeholder('e.g. POS-HR-01')
                            ->extraInputAttributes(['class' => 'font-mono uppercase'])
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('entity_id', $get('entity_id')),
                            ),
                        TextInput::make('job_grade')
                            ->label('Job Grade / Level')
                            ->maxLength(32)
                            ->placeholder('e.g. JG-4, Supervisor, Rank & File')
                            ->extraInputAttributes(['class' => 'font-mono']),
                        TextInput::make('title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull()
                            ->placeholder('e.g. Senior Training Specialist'),
                        Textarea::make('description')
                            ->label('Role Summary')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Active Position')
                            ->default(true),
                    ]),
            ]);
    }
}
