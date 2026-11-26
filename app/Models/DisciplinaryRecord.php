<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DisciplinaryRecord extends Model
{
    use BelongsToEntity;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'entity_id',
        'case_number',
        'incident_date',
        'violation_category',
        'description',
        'status',
        'sanction',
        'suspension_days',
        'penalty_points',
        'memo_date',
        'served_at',
        'employee_explanation',
        'hr_findings',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'memo_date' => 'date',
            'served_at' => 'datetime',
            'suspension_days' => 'integer',
            'penalty_points' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['employee_id', 'case_number', 'violation_category', 'sanction', 'penalty_points', 'status'])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
