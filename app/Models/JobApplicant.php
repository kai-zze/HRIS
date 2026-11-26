<?php

namespace App\Models;

use App\Enums\ApplicantStage;
use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobApplicant extends Model
{
    use BelongsToEntity;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'job_posting_id',
        'entity_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_number',
        'resume_path',
        'stage',
        'rating',
        'interview_notes',
        'expected_salary',
        'hired_employee_id',
        'hired_at',
    ];

    protected function casts(): array
    {
        return [
            'stage' => ApplicantStage::class,
            'rating' => 'integer',
            'expected_salary' => 'decimal:2',
            'hired_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'stage', 'hired_at', 'rating'])
            ->logOnlyDirty();
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function hiredEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'hired_employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
