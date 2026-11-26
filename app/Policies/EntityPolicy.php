<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Entity;
use App\Models\User;

class EntityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            RoleName::SuperAdmin->value,
            RoleName::HrAdmin->value,
        ]) && $user->can('entities.view');
    }

    public function view(User $user, Entity $entity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('entities.view') && $user->entity_id === $entity->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Entity $entity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('entities.update') && $user->entity_id === $entity->id;
    }

    public function delete(User $user, Entity $entity): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Entity $entity): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Entity $entity): bool
    {
        return false;
    }
}
