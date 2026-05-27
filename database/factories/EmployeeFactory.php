<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'document' => $this->faker->unique()->numerify('###########'),
            'job_title' => $this->faker->jobTitle(),
            'registration_number' => $this->faker->unique()->numerify('###########'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}

