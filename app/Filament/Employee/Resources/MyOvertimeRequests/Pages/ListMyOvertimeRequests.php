<?php

namespace App\Filament\Employee\Resources\MyOvertimeRequests\Pages;

use App\Filament\Employee\Resources\MyOvertimeRequests\MyOvertimeRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyOvertimeRequests extends ListRecords
{
    protected static string $resource = MyOvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('File OT Request')
                ->icon('heroicon-o-plus'),
        ];
    }
}
