<?php

namespace App\Filament\Resources\ThirteenthMonths\Pages;

use App\Filament\Resources\ThirteenthMonths\ThirteenthMonthResource;
use App\Models\Entity;
use App\Services\EntityContextService;
use App\Services\ThirteenthMonthService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListThirteenthMonths extends ListRecords
{
    protected static string $resource = ThirteenthMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compute_13th')
                ->label('Compute 13th Month Pay')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->form([
                    Select::make('entity_id')
                        ->label('Entity')
                        ->options(fn () => Entity::all()->pluck('name', 'id'))
                        ->default(fn () => app(EntityContextService::class)->activeEntityId() ?? Entity::first()?->id)
                        ->required(),
                    TextInput::make('year')
                        ->label('Calendar Year')
                        ->numeric()
                        ->default((int) date('Y'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $records = app(ThirteenthMonthService::class)->computeEntityThirteenthMonth(
                        (int) $data['entity_id'],
                        (int) $data['year']
                    );

                    Notification::make()
                        ->title('13th Month Pay Computed')
                        ->body("Successfully generated 13th month records for " . count($records) . " employees.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
