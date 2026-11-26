<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Definition')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Hidden::make('guard_name')
                            ->default('web'),
                    ]),

                Section::make('Module Permissions')
                    ->description('Granular capabilities granted to users holding this role.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->relationship(titleAttribute: 'name')
                            ->columns(['default' => 1, 'md' => 2, 'xl' => 3])
                            ->searchable()
                            ->bulkToggleable(),
                    ]),
            ]);
    }
}
