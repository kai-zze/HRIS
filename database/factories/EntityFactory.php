<?php

namespace Database\Factories;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entity>
 */
class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition(): array
    {
        $code = strtoupper($this->faker->unique()->lexify('ENT-???'));

        return [
            'code' => $code,
            'name' => $this->faker->company() . ' Inc.',
            'short_name' => $code,
            'tin' => $this->faker->numerify('###-###-###-000'),
            'sss_employer_number' => $this->faker->numerify('##-#######-#'),
            'philhealth_employer_number' => $this->faker->numerify('##-#########-#'),
            'pagibig_employer_number' => $this->faker->numerify('####-####-####'),
            'address' => $this->faker->address(),
            'contact_number' => '+63 2 ' . $this->faker->numerify('8### ####'),
            'email' => strtolower($code) . '@km100group.ph',
            'is_active' => true,
        ];
    }
}
