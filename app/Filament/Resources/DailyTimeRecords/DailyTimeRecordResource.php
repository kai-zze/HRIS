<?php

namespace App\Filament\Resources\DailyTimeRecords;

use App\Filament\Resources\DailyTimeRecords\Pages\CreateDailyTimeRecord;
use App\Filament\Resources\DailyTimeRecords\Pages\EditDailyTimeRecord;
use App\Filament\Resources\DailyTimeRecords\Pages\ListDailyTimeRecords;
use App\Filament\Resources\DailyTimeRecords\Schemas\DailyTimeRecordForm;
use App\Filament\Resources\DailyTimeRecords\Tables\DailyTimeRecordsTable;
use App\Models\DailyTimeRecord;
use App\Models\User;
use App\Services\EntityContextService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DailyTimeRecordResource extends Resource
{
    protected static ?string $model = DailyTimeRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Time & Attendance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Daily Time Records (DTR)';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['employee.entity', 'employee.department']);

        $user = Auth::user();
        if ($user instanceof User && ! $user->isSuperAdmin()) {
            $query->where('entity_id', $user->entity_id ?? -1);
        } else {
            $activeEntityId = app(EntityContextService::class)->activeEntityId();
            if ($activeEntityId && $activeEntityId > 0) {
                $query->where('entity_id', $activeEntityId);
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return DailyTimeRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyTimeRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailyTimeRecords::route('/'),
            'create' => CreateDailyTimeRecord::route('/create'),
            'edit' => EditDailyTimeRecord::route('/{record}/edit'),
        ];
    }
}
