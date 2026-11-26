<?php

namespace App\Filament\Resources\PerformanceEvaluations;

use App\Filament\Resources\PerformanceEvaluations\Pages\CreatePerformanceEvaluation;
use App\Filament\Resources\PerformanceEvaluations\Pages\EditPerformanceEvaluation;
use App\Filament\Resources\PerformanceEvaluations\Pages\ListPerformanceEvaluations;
use App\Filament\Resources\PerformanceEvaluations\Schemas\PerformanceEvaluationForm;
use App\Filament\Resources\PerformanceEvaluations\Tables\PerformanceEvaluationsTable;
use App\Models\PerformanceEvaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PerformanceEvaluationResource extends Resource
{
    protected static ?string $model = PerformanceEvaluation::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string | UnitEnum | null $navigationGroup = 'Performance & Growth';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return PerformanceEvaluationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerformanceEvaluationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPerformanceEvaluations::route('/'),
            'create' => CreatePerformanceEvaluation::route('/create'),
            'edit' => EditPerformanceEvaluation::route('/{record}/edit'),
        ];
    }
}
