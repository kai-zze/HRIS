<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhilhealthSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'premium_rate_percent',
        'employee_share_percent',
        'employer_share_percent',
        'income_floor',
        'income_ceiling',
        'effective_year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'premium_rate_percent' => 'decimal:2',
            'employee_share_percent' => 'decimal:2',
            'employer_share_percent' => 'decimal:2',
            'income_floor' => 'decimal:2',
            'income_ceiling' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
