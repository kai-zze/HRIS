<?php

namespace App\Filament\Resources\EmployeeLoans;

use App\Filament\Resources\EmployeeLoans\Pages\CreateEmployeeLoan;
use App\Filament\Resources\EmployeeLoans\Pages\EditEmployeeLoan;
use App\Filament\Resources\EmployeeLoans\Pages\ListEmployeeLoans;
use App\Filament\Resources\EmployeeLoans\Schemas\EmployeeLoanForm;
use App\Filament\Resources\EmployeeLoans\Tables\EmployeeLoansTable;
use App\Models\EmployeeLoan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeLoanResource extends Resource
{
    protected static ?string $model = EmployeeLoan::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static string | UnitEnum | null $navigationGroup = 'Payroll & Compensation';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return EmployeeLoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeLoansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeLoans::route('/'),
            'create' => CreateEmployeeLoan::route('/create'),
            'edit' => EditEmployeeLoan::route('/{record}/edit'),
        ];
    }
}
