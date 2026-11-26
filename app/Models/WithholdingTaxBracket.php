<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingTaxBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_frequency',
        'bracket_level',
        'range_min',
        'range_max',
        'base_tax',
        'excess_rate_percent',
        'effective_year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'range_min' => 'decimal:2',
            'range_max' => 'decimal:2',
            'base_tax' => 'decimal:2',
            'excess_rate_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
