<?php

namespace App\Filament\Employee\Resources\MyDailyTimeRecords\Pages;

use App\Filament\Employee\Resources\MyDailyTimeRecords\MyDailyTimeRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListMyDailyTimeRecords extends ListRecords
{
    protected static string $resource = MyDailyTimeRecordResource::class;
}
