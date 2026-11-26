<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('departments.view');
    }

    public function view(User $user, Department $department): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('departments.view') && $user->entity_id === $department->entity_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('departments.create');
    }

    public function update(User $user, Department $department): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('departments.update') && $user->entity_id === $department->entity_id;
    }

    public function delete(User $user, Department $department): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('departments.delete') && $user->entity_id === $department->entity_id;
    }

    public function restore(User $user, Department $department): bool
    {
        return $this->delete($user, $department);
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return $user->isSuperAdmin();
    }
}
