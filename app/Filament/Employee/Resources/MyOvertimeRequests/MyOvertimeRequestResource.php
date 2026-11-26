<?php

namespace App\Filament\Employee\Resources\MyOvertimeRequests;

use App\Filament\Employee\Resources\MyOvertimeRequests\Pages\CreateMyOvertimeRequest;
use App\Filament\Employee\Resources\MyOvertimeRequests\Pages\ListMyOvertimeRequests;
use App\Filament\Employee\Resources\MyOvertimeRequests\Schemas\MyOvertimeRequestForm;
use App\Filament\Employee\Resources\MyOvertimeRequests\Tables\MyOvertimeRequestsTable;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MyOvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | UnitEnum | null $navigationGroup = 'My Attendance & Leave';

    protected static ?string $navigationLabel = 'My Overtime Requests';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->where('employee_id', $employee?->id ?? 0);
    }

    public static function form(Schema $schema): Schema
    {
        return MyOvertimeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyOvertimeRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyOvertimeRequests::route('/'),
            'create' => CreateMyOvertimeRequest::route('/create'),
        ];
    }
}
