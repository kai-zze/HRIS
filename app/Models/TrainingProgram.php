<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TrainingProgram extends Model
{
    use BelongsToEntity;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'title',
        'description',
        'provider',
        'category',
        'duration_hours',
        'trainer_name',
        'trainer_credentials',
        'start_date',
        'end_date',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'category', 'provider', 'start_date'])
            ->logOnlyDirty();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class);
    }
}
