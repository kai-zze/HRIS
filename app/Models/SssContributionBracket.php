<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SssContributionBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'range_min',
        'range_max',
        'monthly_salary_credit',
        'regular_employee_share',
        'regular_employer_share',
        'ec_employer_share',
        'wisp_employee_share',
        'wisp_employer_share',
        'effective_year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'range_min' => 'decimal:2',
            'range_max' => 'decimal:2',
            'monthly_salary_credit' => 'decimal:2',
            'regular_employee_share' => 'decimal:2',
            'regular_employer_share' => 'decimal:2',
            'ec_employer_share' => 'decimal:2',
            'wisp_employee_share' => 'decimal:2',
            'wisp_employer_share' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
