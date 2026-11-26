<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TrainingParticipant extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'training_program_id',
        'employee_id',
        'status',
        'certificate_number',
        'certificate_issued_at',
        'certificate_expiry_date',
        'score_rating',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'certificate_issued_at' => 'date',
            'certificate_expiry_date' => 'date',
            'score_rating' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['employee_id', 'training_program_id', 'status', 'certificate_number'])
            ->logOnlyDirty();
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
