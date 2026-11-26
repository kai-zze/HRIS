<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('users.view') && $user->entity_id === $model->entity_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('users.update')
            && $user->entity_id === $model->entity_id
            && ! $model->isSuperAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('users.delete')
            && $user->entity_id === $model->entity_id
            && ! $model->isSuperAdmin();
    }

    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
