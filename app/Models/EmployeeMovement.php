<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeMovement extends Model
{
    use LogsActivity;

    protected $fillable = [
        'employee_id',
        'movement_type',
        'from_entity_id',
        'to_entity_id',
        'from_department_id',
        'to_department_id',
        'from_position_id',
        'to_position_id',
        'previous_salary',
        'new_salary',
        'effective_date',
        'remarks',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'movement_type' => MovementType::class,
            'effective_date' => 'date',
            'previous_salary' => 'decimal:2',
            'new_salary' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'employee_id',
                'movement_type',
                'from_entity_id',
                'to_entity_id',
                'effective_date',
                'new_salary',
            ])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function fromEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'from_entity_id');
    }

    public function toEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'to_entity_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'from_position_id');
    }

    public function toPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'to_position_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
