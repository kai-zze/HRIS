<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThirteenthMonthRecord extends Model
{
    use BelongsToEntity;
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'employee_id',
        'year',
        'total_basic_pay_earned',
        'months_worked',
        'thirteenth_month_pay',
        'tax_exempt_amount',
        'taxable_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_basic_pay_earned' => 'decimal:2',
            'months_worked' => 'decimal:2',
            'thirteenth_month_pay' => 'decimal:2',
            'tax_exempt_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
        ];
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
