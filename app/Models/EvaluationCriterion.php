<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationCriterion extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'performance_evaluation_id',
        'criterion_name',
        'weight_percentage',
        'score',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'weight_percentage' => 'integer',
            'score' => 'decimal:2',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(PerformanceEvaluation::class, 'performance_evaluation_id');
    }
}
