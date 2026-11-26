<?php

namespace Tests\Feature;

use App\Enums\EmploymentStatus;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2CoreHRTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_seeder_creates_201_records_with_encrypted_ids(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(10, Employee::withoutGlobalScopes()->count());

        $employee = Employee::withoutGlobalScopes()->where('email', 'employee@km100group.ph')->firstOrFail();

        $this->assertNotNull($employee->employee_number);
        $this->assertSame(EmploymentStatus::Regular, $employee->employment_status);
        $this->assertSame('123-456-789-000', $employee->tin);
        $this->assertNotEmpty($employee->masked_tin);
        $this->assertStringContainsString('•', $employee->masked_tin);

        // Check related 201 records
        $this->assertNotEmpty($employee->emergencyContacts);
        $this->assertNotEmpty($employee->educationRecords);
        $this->assertNotEmpty($employee->workHistories);
        $this->assertNotEmpty($employee->documents);
    }

    public function test_employee_resource_accessible_and_scoped_to_entity(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@km100group.ph')->firstOrFail();
        $this->actingAs($superAdmin);

        $this->get(EmployeeResource::getUrl('index'))->assertOk();

        // Check HR Admin scoping
        $hrKnoah = User::query()->where('email', 'hr.knoah@km100group.ph')->firstOrFail();
        $knoah = Entity::query()->where('code', 'KNOAH')->firstOrFail();

        $this->actingAs($hrKnoah);
        $visibleEmployeeEntities = EmployeeResource::getEloquentQuery()->pluck('entity_id')->unique()->all();
        $this->assertSame([$knoah->id], $visibleEmployeeEntities);
    }
}
