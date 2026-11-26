<?php

namespace App\Models;

use App\Enums\HolidayType;
use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use BelongsToEntity;

    protected $fillable = [
        'entity_id',
        'name',
        'holiday_date',
        'holiday_type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'holiday_type' => HolidayType::class,
        ];
    }
}
