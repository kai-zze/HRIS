<?php

namespace App\Filament\Resources\EvaluationPeriods;

use App\Filament\Resources\EvaluationPeriods\Pages\CreateEvaluationPeriod;
use App\Filament\Resources\EvaluationPeriods\Pages\EditEvaluationPeriod;
use App\Filament\Resources\EvaluationPeriods\Pages\ListEvaluationPeriods;
use App\Filament\Resources\EvaluationPeriods\Schemas\EvaluationPeriodForm;
use App\Filament\Resources\EvaluationPeriods\Tables\EvaluationPeriodsTable;
use App\Models\EvaluationPeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EvaluationPeriodResource extends Resource
{
    protected static ?string $model = EvaluationPeriod::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static string | UnitEnum | null $navigationGroup = 'Performance & Growth';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EvaluationPeriodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluationPeriodsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluationPeriods::route('/'),
            'create' => CreateEvaluationPeriod::route('/create'),
            'edit' => EditEvaluationPeriod::route('/{record}/edit'),
        ];
    }
}
