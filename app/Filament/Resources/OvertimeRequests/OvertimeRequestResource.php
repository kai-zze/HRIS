<?php

namespace App\Filament\Resources\OvertimeRequests;

use App\Filament\Resources\OvertimeRequests\Pages\CreateOvertimeRequest;
use App\Filament\Resources\OvertimeRequests\Pages\EditOvertimeRequest;
use App\Filament\Resources\OvertimeRequests\Pages\ListOvertimeRequests;
use App\Filament\Resources\OvertimeRequests\Schemas\OvertimeRequestForm;
use App\Filament\Resources\OvertimeRequests\Tables\OvertimeRequestsTable;
use App\Models\OvertimeRequest;
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

class OvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Time & Attendance';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['employee.entity', 'employee.department']);

        $user = Auth::user();
        if ($user instanceof User && ! $user->isSuperAdmin()) {
            $query->whereHas('employee', fn ($q) => $q->where('entity_id', $user->entity_id ?? -1));
        } else {
            $activeEntityId = app(EntityContextService::class)->activeEntityId();
            if ($activeEntityId && $activeEntityId > 0) {
                $query->whereHas('employee', fn ($q) => $q->where('entity_id', $activeEntityId));
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OvertimeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimeRequests::route('/'),
            'create' => CreateOvertimeRequest::route('/create'),
            'edit' => EditOvertimeRequest::route('/{record}/edit'),
        ];
    }
}
