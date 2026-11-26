<?php

namespace App\Filament\Employee\Resources\MyDailyTimeRecords;

use App\Filament\Employee\Resources\MyDailyTimeRecords\Pages\ListMyDailyTimeRecords;
use App\Filament\Employee\Resources\MyDailyTimeRecords\Tables\MyDailyTimeRecordsTable;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MyDailyTimeRecordResource extends Resource
{
    protected static ?string $model = DailyTimeRecord::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-finger-print';

    protected static string | UnitEnum | null $navigationGroup = 'My Attendance & Leave';

    protected static ?string $navigationLabel = 'My DTR / Attendance';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->where('employee_id', $employee?->id ?? 0);
    }

    public static function table(Table $table): Table
    {
        return MyDailyTimeRecordsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyDailyTimeRecords::route('/'),
        ];
    }
}
