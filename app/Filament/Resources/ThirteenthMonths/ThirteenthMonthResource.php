<?php

namespace App\Filament\Resources\ThirteenthMonths;

use App\Filament\Resources\ThirteenthMonths\Pages\ListThirteenthMonths;
use App\Filament\Resources\ThirteenthMonths\Tables\ThirteenthMonthsTable;
use App\Models\ThirteenthMonthRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

class ThirteenthMonthResource extends Resource
{
    protected static ?string $model = ThirteenthMonthRecord::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-gift';

    protected static string | UnitEnum | null $navigationGroup = 'Payroll & Compensation';

    protected static ?string $navigationLabel = '13th Month Pay';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return ThirteenthMonthsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThirteenthMonths::route('/'),
        ];
    }
}
