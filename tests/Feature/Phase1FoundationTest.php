<?php

namespace Tests\Feature;

use App\Enums\EntityCode;
use App\Enums\RoleName;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Resources\Entities\EntityResource;
use App\Filament\Resources\Positions\PositionResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Position;
use App\Models\User;
use App\Services\EntityContextService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase1FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_five_entities_and_demo_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(5, Entity::query()->count());

        foreach (EntityCode::cases() as $code) {
            $this->assertDatabaseHas('entities', ['code' => $code->value]);
        }

        $this->assertGreaterThanOrEqual(4, Department::withoutEntityScope()->count());
        $this->assertGreaterThanOrEqual(10, Position::withoutEntityScope()->count());
        $this->assertGreaterThanOrEqual(40, User::query()->count());
        $this->assertDatabaseHas('users', ['email' => 'superadmin@km100group.ph']);
        $this->assertDatabaseHas('users', ['email' => 'hr.knoah@km100group.ph']);
    }

    public function test_entity_statutory_ids_are_encrypted_and_masked(): void
    {
        $this->seed(DatabaseSeeder::class);

        $entity = Entity::query()->where('code', EntityCode::Km100Stc->value)->firstOrFail();

        $rawTin = DB::table('entities')->where('id', $entity->id)->value('tin');

        $this->assertNotSame('009-412-881-000', $rawTin);
        $this->assertSame('009-412-881-000', $entity->tin);
        $this->assertSame('•••••••••••-000', $entity->masked_tin);
    }

    public function test_hr_admin_is_strictly_scoped_to_their_own_entity(): void
    {
        $this->seed(DatabaseSeeder::class);

        $hrKnoah = User::query()->where('email', 'hr.knoah@km100group.ph')->firstOrFail();
        $knoah = Entity::query()->where('code', EntityCode::KnoahMari->value)->firstOrFail();
        $redGoose = Entity::query()->where('code', EntityCode::RedGoose->value)->firstOrFail();

        $this->actingAs($hrKnoah);

        $visibleDepartments = Department::query()->pluck('entity_id')->unique()->all();
        $this->assertSame([$knoah->id], $visibleDepartments);

        $visiblePositions = Position::query()->pluck('entity_id')->unique()->all();
        $this->assertSame([$knoah->id], $visiblePositions);

        $visibleUsers = UserResource::getEloquentQuery()->pluck('entity_id')->unique()->all();
        $this->assertSame([$knoah->id], $visibleUsers);

        $this->assertTrue($hrKnoah->can('view', $knoah));
        $this->assertFalse($hrKnoah->can('view', $redGoose));
        $this->assertFalse($hrKnoah->can('create', Entity::class));
    }

    public function test_super_admin_can_view_all_entities_and_switch_active_entity(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@km100group.ph')->firstOrFail();
        $redGoose = Entity::query()->where('code', EntityCode::RedGoose->value)->firstOrFail();

        $this->actingAs($superAdmin);

        $this->assertGreaterThan(1, Department::query()->pluck('entity_id')->unique()->count());

        $context = app(EntityContextService::class);
        $this->startSession();
        $context->setActiveEntity($redGoose->id);

        $scopedDepartments = Department::query()->pluck('entity_id')->unique()->all();
        $this->assertSame([$redGoose->id], $scopedDepartments);

        $context->setActiveEntity(null);
        $this->assertGreaterThan(1, Department::query()->pluck('entity_id')->unique()->count());
    }

    public function test_employee_role_cannot_access_admin_panel(): void
    {
        $this->seed(DatabaseSeeder::class);

        $employee = User::query()->where('email', 'employee@km100group.ph')->firstOrFail();
        $adminPanel = Filament::getPanel('admin');

        $this->assertTrue($employee->hasRole(RoleName::Employee->value));
        $this->assertFalse($employee->canAccessPanel($adminPanel));
    }

    public function test_admin_panel_login_and_authenticated_resources_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/admin/login')->assertRedirect('/admin');

        $superAdmin = User::query()->where('email', 'superadmin@km100group.ph')->firstOrFail();

        $this->actingAs($superAdmin);
        $this->get('/admin')->assertOk();
        $this->get(EntityResource::getUrl('index'))->assertOk();
        $this->get(DepartmentResource::getUrl('index'))->assertOk();
        $this->get(PositionResource::getUrl('index'))->assertOk();
        $this->get(UserResource::getUrl('index'))->assertOk();
    }
}
