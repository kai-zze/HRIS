<?php

namespace App\Models\Concerns;

use App\Models\Entity;
use App\Models\Scopes\EntityScope;
use App\Services\EntityContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToEntity
{
    public static function bootBelongsToEntity(): void
    {
        static::addGlobalScope(new EntityScope);

        static::creating(function (Model $model): void {
            if (! $model->getAttribute('entity_id')) {
                $activeEntityId = app(EntityContextService::class)->activeEntityId();

                if ($activeEntityId && $activeEntityId > 0) {
                    $model->setAttribute('entity_id', $activeEntityId);
                }
            }
        });
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function scopeWithoutEntityScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(EntityScope::class);
    }
}
