<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkSchedule extends Model
{
    use BelongsToEntity;
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'name',
        'time_in',
        'time_out',
        'break_minutes',
        'grace_period_minutes',
        'working_days',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'working_days' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'break_minutes' => 'integer',
            'grace_period_minutes' => 'integer',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
