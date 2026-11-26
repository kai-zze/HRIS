<?php

namespace App\Filament\Resources\SssContributionBrackets\Pages;

use App\Filament\Resources\SssContributionBrackets\SssContributionBracketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSssContributionBrackets extends ListRecords
{
    protected static string $resource = SssContributionBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
