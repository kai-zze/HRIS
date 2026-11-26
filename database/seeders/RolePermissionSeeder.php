<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'entities.view',
            'entities.create',
            'entities.update',
            'entities.delete',

            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',

            'positions.view',
            'positions.create',
            'positions.update',
            'positions.delete',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',

            'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate(RoleName::SuperAdmin->value, 'web');
        $superAdmin->syncPermissions($permissions);

        $hrAdmin = Role::findOrCreate(RoleName::HrAdmin->value, 'web');
        $hrAdmin->syncPermissions([
            'entities.view',
            'entities.update',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'positions.view',
            'positions.create',
            'positions.update',
            'positions.delete',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
        ]);

        $manager = Role::findOrCreate(RoleName::Manager->value, 'web');
        $manager->syncPermissions([
            'departments.view',
            'positions.view',
        ]);

        $employee = Role::findOrCreate(RoleName::Employee->value, 'web');
        $employee->syncPermissions([]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
