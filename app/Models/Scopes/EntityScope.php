<?php

namespace App\Models\Scopes;

use App\Enums\RoleName;
use App\Models\User;
use App\Services\EntityContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EntityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::hasUser()) {
            return;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $column = $model->qualifyColumn('entity_id');

        if (! $user->hasRole(RoleName::SuperAdmin->value)) {
            $builder->where($column, $user->entity_id ?? -1);

            return;
        }

        $activeEntityId = app(EntityContextService::class)->activeEntityId();

        if ($activeEntityId !== null && $activeEntityId > 0) {
            $builder->where($column, $activeEntityId);
        }
    }
}
