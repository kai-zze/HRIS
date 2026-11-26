<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            EntitySeeder::class,
            DepartmentPositionSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            TimeAttendanceLeaveSeeder::class,
            AnnouncementSeeder::class,
            Phase5RecruitmentPerformanceDisciplineTrainingSeeder::class,
            StatutoryTablesSeeder::class,
            PayrollSeeder::class,
        ]);
    }
}
