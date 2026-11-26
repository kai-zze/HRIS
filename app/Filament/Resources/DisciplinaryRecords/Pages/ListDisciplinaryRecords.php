<?php

namespace App\Filament\Resources\DisciplinaryRecords\Pages;

use App\Filament\Resources\DisciplinaryRecords\DisciplinaryRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisciplinaryRecords extends ListRecords
{
    protected static string $resource = DisciplinaryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('File Incident / NTE')
                ->icon('heroicon-o-plus'),
        ];
    }
}
