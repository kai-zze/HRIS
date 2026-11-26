<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EvaluationPeriod extends Model
{
    use BelongsToEntity;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'name',
        'period_type',
        'start_date',
        'end_date',
        'status',
        'weight_evaluation',
        'weight_attendance',
        'weight_training',
        'weight_discipline',
        'weight_commendations',
        'finalized_at',
        'finalized_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'finalized_at' => 'datetime',
            'weight_evaluation' => 'integer',
            'weight_attendance' => 'integer',
            'weight_training' => 'integer',
            'weight_discipline' => 'integer',
            'weight_commendations' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'finalized_at'])
            ->logOnlyDirty();
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PerformanceEvaluation::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(EmployeePerformanceRanking::class);
    }

    public function finalizedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
