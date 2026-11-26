<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagibigSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mandatory_employee_share',
        'mandatory_employer_share',
        'max_compensation_cap',
        'employee_rate_percent',
        'employer_rate_percent',
        'effective_year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mandatory_employee_share' => 'decimal:2',
            'mandatory_employer_share' => 'decimal:2',
            'max_compensation_cap' => 'decimal:2',
            'employee_rate_percent' => 'decimal:2',
            'employer_rate_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
