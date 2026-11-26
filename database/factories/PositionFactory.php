<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Entity;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'department_id' => Department::factory(),
            'code' => strtoupper($this->faker->unique()->lexify('POS-???')),
            'title' => $this->faker->jobTitle(),
            'job_grade' => $this->faker->randomElement(['JG-1', 'JG-2', 'JG-3', 'JG-4', 'SUP-1', 'MGR-1']),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
