<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('employees.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('employees.view') && $user->entity_id === $employee->entity_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->can('employees.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('employees.update') && $user->entity_id === $employee->entity_id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('employees.delete') && $user->entity_id === $employee->entity_id;
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $this->delete($user, $employee);
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin();
    }
}
