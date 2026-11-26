<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use BelongsToEntity;

    protected $fillable = [
        'entity_id',
        'code',
        'name',
        'description',
        'default_credit_days',
        'is_paid',
        'is_convertible_to_cash',
        'requires_medical_certificate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'is_convertible_to_cash' => 'boolean',
            'requires_medical_certificate' => 'boolean',
            'is_active' => 'boolean',
            'default_credit_days' => 'integer',
        ];
    }

    public function leaveCredits(): HasMany
    {
        return $this->hasMany(LeaveCredit::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
