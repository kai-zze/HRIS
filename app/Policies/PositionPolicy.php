<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('positions.view');
    }

    public function view(User $user, Position $position): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('positions.view') && $user->entity_id === $position->entity_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('positions.create');
    }

    public function update(User $user, Position $position): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('positions.update') && $user->entity_id === $position->entity_id;
    }

    public function delete(User $user, Position $position): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('positions.delete') && $user->entity_id === $position->entity_id;
    }

    public function restore(User $user, Position $position): bool
    {
        return $this->delete($user, $position);
    }

    public function forceDelete(User $user, Position $position): bool
    {
        return $user->isSuperAdmin();
    }
}
