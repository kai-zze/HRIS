<?php

namespace App\Filament\Resources\Departments\Schemas;

use App\Models\Entity;
use App\Models\User;
use App\Services\EntityContextService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Department Details')
                    ->columns(2)
                    ->schema([
                        Select::make('entity_id')
                            ->label('Entity')
                            ->options(fn (): array => Entity::query()
                                ->where('is_active', true)
                                ->orderBy('name')
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
                            ->live(),
                        TextInput::make('code')
                            ->label('Department Code')
                            ->required()
                            ->maxLength(32)
                            ->placeholder('e.g. HR, FIN, TRG')
                            ->extraInputAttributes(['class' => 'font-mono uppercase'])
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('entity_id', $get('entity_id')),
                            ),
                        TextInput::make('name')
                            ->label('Department Name')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull()
                            ->placeholder('e.g. Human Resources & Administration'),
                        Textarea::make('description')
                            ->label('Mandate / Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Active Department')
                            ->default(true),
                    ]),
            ]);
    }
}
