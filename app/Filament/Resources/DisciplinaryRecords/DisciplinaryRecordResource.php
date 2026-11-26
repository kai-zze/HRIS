<?php

namespace App\Filament\Resources\DisciplinaryRecords;

use App\Filament\Resources\DisciplinaryRecords\Pages\CreateDisciplinaryRecord;
use App\Filament\Resources\DisciplinaryRecords\Pages\EditDisciplinaryRecord;
use App\Filament\Resources\DisciplinaryRecords\Pages\ListDisciplinaryRecords;
use App\Filament\Resources\DisciplinaryRecords\Schemas\DisciplinaryRecordForm;
use App\Filament\Resources\DisciplinaryRecords\Tables\DisciplinaryRecordsTable;
use App\Models\DisciplinaryRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DisciplinaryRecordResource extends Resource
{
    protected static ?string $model = DisciplinaryRecord::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string | UnitEnum | null $navigationGroup = 'Discipline & Employee Relations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DisciplinaryRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisciplinaryRecordsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisciplinaryRecords::route('/'),
            'create' => CreateDisciplinaryRecord::route('/create'),
            'edit' => EditDisciplinaryRecord::route('/{record}/edit'),
        ];
    }
}
