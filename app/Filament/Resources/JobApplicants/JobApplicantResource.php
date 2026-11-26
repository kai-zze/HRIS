<?php

namespace App\Filament\Resources\JobApplicants;

use App\Filament\Resources\JobApplicants\Pages\CreateJobApplicant;
use App\Filament\Resources\JobApplicants\Pages\EditJobApplicant;
use App\Filament\Resources\JobApplicants\Pages\ListJobApplicants;
use App\Filament\Resources\JobApplicants\Schemas\JobApplicantForm;
use App\Filament\Resources\JobApplicants\Tables\JobApplicantsTable;
use App\Models\JobApplicant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class JobApplicantResource extends Resource
{
    protected static ?string $model = JobApplicant::class;

    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | UnitEnum | null $navigationGroup = 'Recruitment & Talent';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return JobApplicantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicants::route('/'),
            'create' => CreateJobApplicant::route('/create'),
            'edit' => EditJobApplicant::route('/{record}/edit'),
        ];
    }
}
