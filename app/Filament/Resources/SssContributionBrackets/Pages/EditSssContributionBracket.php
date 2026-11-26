<?php

namespace App\Filament\Resources\SssContributionBrackets\Pages;

use App\Filament\Resources\SssContributionBrackets\SssContributionBracketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSssContributionBracket extends EditRecord
{
    protected static string $resource = SssContributionBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
