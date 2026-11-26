<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntityContextService
{
    public const SESSION_KEY = 'km100_active_entity_id';

    public function activeEntityId(): ?int
    {
        $user = Auth::hasUser() ? Auth::user() : null;

        if (! $user instanceof User) {
            return null;
        }

        if (! $user->hasRole(RoleName::SuperAdmin->value)) {
            return $user->entity_id ? (int) $user->entity_id : -1;
        }

        if (! app()->bound('session') || ! session()->isStarted()) {
            return null;
        }

        $entityId = session(self::SESSION_KEY);

        return $entityId ? (int) $entityId : null;
    }

    public function activeEntity(): ?Entity
    {
        $entityId = $this->activeEntityId();

        if (! $entityId || $entityId < 1) {
            return null;
        }

        return Entity::query()->find($entityId);
    }

    public function setActiveEntity(?int $entityId): void
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->hasRole(RoleName::SuperAdmin->value)) {
            return;
        }

        if ($entityId === null || $entityId <= 0) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        if (Entity::query()->whereKey($entityId)->exists()) {
            session()->put(self::SESSION_KEY, $entityId);
        }
    }

    public function isScopedToSingleEntity(): bool
    {
        $entityId = $this->activeEntityId();

        return $entityId !== null && $entityId > 0;
    }

    public function canSwitchEntities(?User $user = null): bool
    {
        $user ??= Auth::hasUser() ? Auth::user() : null;

        return $user instanceof User && $user->hasRole(RoleName::SuperAdmin->value);
    }
}
