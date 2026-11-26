<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeePerformanceRanking extends Model
{
    use BelongsToEntity;
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'evaluation_period_id',
        'employee_id',
        'entity_id',
        'department_id',
        'position_id',
        'evaluation_score',
        'attendance_score',
        'training_score',
        'discipline_score',
        'commendation_score',
        'calculated_total_score',
        'manual_adjustment_score',
        'manual_adjustment_reason',
        'final_score',
        'rank_overall',
        'rank_department',
        'rank_position',
        'is_finalized',
    ];

    protected function casts(): array
    {
        return [
            'evaluation_score' => 'decimal:2',
            'attendance_score' => 'decimal:2',
            'training_score' => 'decimal:2',
            'discipline_score' => 'decimal:2',
            'commendation_score' => 'decimal:2',
            'calculated_total_score' => 'decimal:2',
            'manual_adjustment_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'rank_overall' => 'integer',
            'rank_department' => 'integer',
            'rank_position' => 'integer',
            'is_finalized' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['employee_id', 'final_score', 'rank_overall', 'rank_department', 'manual_adjustment_score'])
            ->logOnlyDirty();
    }

    public function evaluationPeriod(): BelongsTo
    {
        return $this->belongsTo(EvaluationPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
