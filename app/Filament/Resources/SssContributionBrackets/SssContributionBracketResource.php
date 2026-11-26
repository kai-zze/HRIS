<?php

namespace App\Filament\Resources\SssContributionBrackets;

use App\Filament\Resources\SssContributionBrackets\Pages\CreateSssContributionBracket;
use App\Filament\Resources\SssContributionBrackets\Pages\EditSssContributionBracket;
use App\Filament\Resources\SssContributionBrackets\Pages\ListSssContributionBrackets;
use App\Filament\Resources\SssContributionBrackets\Schemas\SssContributionBracketForm;
use App\Filament\Resources\SssContributionBrackets\Tables\SssContributionBracketsTable;
use App\Models\SssContributionBracket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SssContributionBracketResource extends Resource
{
    protected static ?string $model = SssContributionBracket::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-table-cells';

    protected static string | UnitEnum | null $navigationGroup = 'Payroll & Compensation';

    protected static ?string $navigationLabel = 'SSS Schedule';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SssContributionBracketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SssContributionBracketsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSssContributionBrackets::route('/'),
            'create' => CreateSssContributionBracket::route('/create'),
            'edit' => EditSssContributionBracket::route('/{record}/edit'),
        ];
    }
}
