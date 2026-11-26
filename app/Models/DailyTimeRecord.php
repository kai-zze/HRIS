<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTimeRecord extends Model
{
    use BelongsToEntity;

    protected $fillable = [
        'employee_id',
        'entity_id',
        'record_date',
        'time_in',
        'time_out',
        'late_minutes',
        'undertime_minutes',
        'regular_hours',
        'overtime_hours',
        'night_diff_hours',
        'status',
        'is_holiday',
        'holiday_id',
        'leave_request_id',
        'overtime_request_id',
        'source',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'status' => AttendanceStatus::class,
            'late_minutes' => 'integer',
            'undertime_minutes' => 'integer',
            'regular_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'night_diff_hours' => 'decimal:2',
            'is_holiday' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function overtimeRequest(): BelongsTo
    {
        return $this->belongsTo(OvertimeRequest::class);
    }
}
