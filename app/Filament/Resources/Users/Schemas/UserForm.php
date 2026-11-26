<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleName;
use App\Models\Entity;
use App\Models\User;
use App\Services\EntityContextService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Profile')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('email')
                            ->label('Work Email')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true),
                        Select::make('entity_id')
                            ->label('Assigned Entity')
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
                            ->required(fn (): bool => Auth::user() instanceof User && ! Auth::user()->isSuperAdmin())
                            ->searchable()
                            ->placeholder('Global (Super Admin only)'),
                        Select::make('roles')
                            ->label('Assigned Role')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): Builder {
                                    $user = Auth::user();
                                    if ($user instanceof User && ! $user->isSuperAdmin()) {
                                        $query->where('name', '!=', RoleName::SuperAdmin->value);
                                    }

                                    return $query->orderBy('id');
                                },
                            )
                            ->multiple()
                            ->preload()
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(8)
                            ->helperText('Leave blank when editing to keep the current password.'),
                        Toggle::make('is_active')
                            ->label('Account Enabled')
                            ->default(true)
                            ->helperText('Disabled accounts cannot sign in to either HRIS panel.'),
                    ]),
            ]);
    }
}
