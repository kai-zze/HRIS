<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'code' => strtoupper($this->faker->unique()->lexify('DEPT-???')),
            'name' => $this->faker->randomElement([
                'Human Resources',
                'Finance & Accounting',
                'Operations',
                'Training & Development',
                'Sales & Marketing',
                'Information Technology',
            ]),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
