<?php

namespace App\Filament\Resources\PerformanceEvaluations\Pages;

use App\Filament\Resources\PerformanceEvaluations\PerformanceEvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPerformanceEvaluations extends ListRecords
{
    protected static string $resource = PerformanceEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Performance Appraisal')
                ->icon('heroicon-o-plus'),
        ];
    }
}
