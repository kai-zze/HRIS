<?php

namespace App\Filament\Resources\EmployeePerformanceRankings;

use App\Filament\Resources\EmployeePerformanceRankings\Pages\ListEmployeePerformanceRankings;
use App\Filament\Resources\EmployeePerformanceRankings\Tables\EmployeePerformanceRankingsTable;
use App\Models\EmployeePerformanceRanking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

class EmployeePerformanceRankingResource extends Resource
{
    protected static ?string $model = EmployeePerformanceRanking::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';

    protected static string | UnitEnum | null $navigationGroup = 'Performance & Growth';

    protected static ?string $navigationLabel = 'Top Performers Leaderboard';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return EmployeePerformanceRankingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeePerformanceRankings::route('/'),
        ];
    }
}
