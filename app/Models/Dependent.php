<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependent extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'birthdate',
        'is_tax_dependent',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'is_tax_dependent' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
