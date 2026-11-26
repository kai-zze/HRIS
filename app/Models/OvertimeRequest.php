<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OvertimeRequest extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'overtime_date',
        'start_time',
        'end_time',
        'hours_requested',
        'purpose',
        'status',
        'manager_id',
        'manager_action_at',
        'manager_notes',
        'hr_id',
        'hr_action_at',
        'hr_notes',
    ];

    protected function casts(): array
    {
        return [
            'overtime_date' => 'date',
            'hours_requested' => 'decimal:2',
            'status' => RequestStatus::class,
            'manager_action_at' => 'datetime',
            'hr_action_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['employee_id', 'overtime_date', 'hours_requested', 'status'])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function hr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_id');
    }
}
