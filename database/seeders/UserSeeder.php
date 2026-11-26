<?php

namespace Database\Seeders;

use App\Enums\EntityCode;
use App\Enums\RoleName;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password123!');

        // 1. Group Super Admin (global access, no fixed entity lock)
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@km100group.ph'],
            [
                'entity_id' => null,
                'name' => 'Marco Antonio Reyes',
                'password' => $password,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([RoleName::SuperAdmin->value]);

        // 2. HR Admins & Managers per entity
        $leadership = [
            EntityCode::Km100Stc->value => [
                'hr' => ['name' => 'Clarissa Mendoza', 'email' => 'hr.km100@km100group.ph'],
                'manager' => ['name' => 'Engr. Rafael Villanueva', 'email' => 'manager.km100@km100group.ph'],
            ],
            EntityCode::KnoahMari->value => [
                'hr' => ['name' => 'Patricia Santos', 'email' => 'hr.knoah@km100group.ph'],
                'manager' => ['name' => 'Gabriel Bautista', 'email' => 'manager.knoah@km100group.ph'],
            ],
            EntityCode::RedGoose->value => [
                'hr' => ['name' => 'Jerome Aquino', 'email' => 'hr.redgoose@km100group.ph'],
                'manager' => ['name' => 'Danilo Pascual', 'email' => 'manager.redgoose@km100group.ph'],
            ],
            EntityCode::Stc->value => [
                'hr' => ['name' => 'Marites Navarro', 'email' => 'hr.stc@km100group.ph'],
                'manager' => ['name' => 'Rolando Cruz', 'email' => 'manager.stc@km100group.ph'],
            ],
            EntityCode::Petc->value => [
                'hr' => ['name' => 'Camille Soriano', 'email' => 'hr.petc@km100group.ph'],
                'manager' => ['name' => 'Noel Mercado', 'email' => 'manager.petc@km100group.ph'],
            ],
        ];

        $entities = Entity::query()->get()->keyBy('code');

        foreach ($leadership as $code => $accounts) {
            $entity = $entities->get($code);

            if (! $entity) {
                continue;
            }

            $hrUser = User::query()->updateOrCreate(
                ['email' => $accounts['hr']['email']],
                [
                    'entity_id' => $entity->id,
                    'name' => $accounts['hr']['name'],
                    'password' => $password,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
            $hrUser->syncRoles([RoleName::HrAdmin->value]);

            $managerUser = User::query()->updateOrCreate(
                ['email' => $accounts['manager']['email']],
                [
                    'entity_id' => $entity->id,
                    'name' => $accounts['manager']['name'],
                    'password' => $password,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
            $managerUser->syncRoles([RoleName::Manager->value]);
        }

        // 3. 32 Employee demo accounts distributed across the 5 KM100 entities
        $employeesByEntity = [
            EntityCode::Km100Stc->value => [
                'Angelo Dela Cruz', 'Bianca Ramos', 'Carlo Fernandez', 'Denise Bautista',
                'Eduardo Garcia', 'Fatima Lim', 'Gianna Torres', 'Henrik Ocampo',
            ],
            EntityCode::KnoahMari->value => [
                'Irene Castillo', 'Jayson Rivera', 'Katrina Flores', 'Lorenzo Diaz',
                'Mikaela Gonzales', 'Nathaniel Tan', 'Olivia Pangilinan',
            ],
            EntityCode::RedGoose->value => [
                'Paulo Salazar', 'Queenie Morales', 'Rico Tolentino', 'Samantha Uy',
                'Tristan Velasco', 'Ursula De Leon',
            ],
            EntityCode::Stc->value => [
                'Vincent Magno', 'Wendy Castro', 'Xavier Domingo', 'Yvette Manalo',
                'Zachary Alonzo', 'Abigail Vergara',
            ],
            EntityCode::Petc->value => [
                'Bryan Evangelista', 'Cherry Ann Lopez', 'Derrick Roxas', 'Elaine Dizon',
                'Francis Corpuz',
            ],
        ];

        $index = 1;
        foreach ($employeesByEntity as $code => $names) {
            $entity = $entities->get($code);

            if (! $entity) {
                continue;
            }

            foreach ($names as $name) {
                $slug = strtolower(str_replace([' ', '.'], ['.', ''], $name));
                $email = $index === 1
                    ? 'employee@km100group.ph'
                    : "{$slug}@km100group.ph";

                $employee = User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'entity_id' => $entity->id,
                        'name' => $name,
                        'password' => $password,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ],
                );
                $employee->syncRoles([RoleName::Employee->value]);
                $index++;
            }
        }
    }
}
