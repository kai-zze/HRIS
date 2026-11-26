<?php

namespace App\Filament\Employee\Resources\MyLeaveRequests;

use App\Filament\Employee\Resources\MyLeaveRequests\Pages\CreateMyLeaveRequest;
use App\Filament\Employee\Resources\MyLeaveRequests\Pages\ListMyLeaveRequests;
use App\Filament\Employee\Resources\MyLeaveRequests\Schemas\MyLeaveRequestForm;
use App\Filament\Employee\Resources\MyLeaveRequests\Tables\MyLeaveRequestsTable;
use App\Models\Employee;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MyLeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | UnitEnum | null $navigationGroup = 'My Attendance & Leave';

    protected static ?string $navigationLabel = 'My Leave Applications';

    protected static ?int $navigationSort = 2;

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
        return MyLeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyLeaveRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyLeaveRequests::route('/'),
            'create' => CreateMyLeaveRequest::route('/create'),
        ];
    }
}
