<?php

namespace App\Models;

use App\Enums\CivilStatus;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use BelongsToEntity;
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'entity_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthdate',
        'sex',
        'civil_status',
        'email',
        'contact_number',
        'residential_address',
        'permanent_address',
        'photo_path',
        'tin',
        'sss_number',
        'philhealth_number',
        'pagibig_number',
        'bank_account_number',
        'bank_name',
        'department_id',
        'position_id',
        'supervisor_id',
        'employment_status',
        'date_hired',
        'regularization_date',
        'work_schedule',
        'basic_salary',
        'separation_date',
        'separation_type',
        'separation_reason',
        'clearance_completed',
        'final_pay_status',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'date_hired' => 'date',
            'regularization_date' => 'date',
            'separation_date' => 'date',
            'sex' => Gender::class,
            'civil_status' => CivilStatus::class,
            'employment_status' => EmploymentStatus::class,
            'tin' => 'encrypted',
            'sss_number' => 'encrypted',
            'philhealth_number' => 'encrypted',
            'pagibig_number' => 'encrypted',
            'bank_account_number' => 'encrypted',
            'basic_salary' => 'decimal:2',
            'clearance_completed' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'entity_id',
                'employee_number',
                'first_name',
                'last_name',
                'department_id',
                'position_id',
                'employment_status',
                'date_hired',
                'basic_salary',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('documents');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function salaryStructure(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function salaryAllowances(): HasMany
    {
        return $this->hasMany(SalaryAllowance::class);
    }

    public function salaryDeductions(): HasMany
    {
        return $this->hasMany(SalaryDeduction::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function thirteenthMonthRecords(): HasMany
    {
        return $this->hasMany(ThirteenthMonthRecord::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(Dependent::class);
    }

    public function educationRecords(): HasMany
    {
        return $this->hasMany(EducationRecord::class);
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(EmployeeMovement::class)->orderByDesc('effective_date');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class)->orderByDesc('created_at');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}");
    }

    public function getMaskedTinAttribute(): string
    {
        return Entity::maskIdentifier($this->tin);
    }

    public function getMaskedSssAttribute(): string
    {
        return Entity::maskIdentifier($this->sss_number);
    }

    public function getMaskedPhilhealthAttribute(): string
    {
        return Entity::maskIdentifier($this->philhealth_number);
    }

    public function getMaskedPagibigAttribute(): string
    {
        return Entity::maskIdentifier($this->pagibig_number);
    }

    public function getMaskedBankAccountAttribute(): string
    {
        return Entity::maskIdentifier($this->bank_account_number);
    }

    /**
     * Auto-generate sequential employee number per entity (e.g. KM100-2026-0001)
     */
    public static function generateEmployeeNumber(int $entityId): string
    {
        $entity = Entity::query()->find($entityId);
        $prefix = $entity ? $entity->code : 'EMP';
        $year = date('Y');

        $latest = static::withoutGlobalScopes()
            ->where('entity_id', $entityId)
            ->where('employee_number', 'LIKE', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest) {
            $parts = explode('-', $latest->employee_number);
            $lastSeq = (int) end($parts);
            $sequence = $lastSeq + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }
}
