<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-7 days', 'now');
        $endedAt = (clone $startedAt)->modify('+'.random_int(1, 10).' hours');

        return [
            'employee_id' => Employee::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => [
            'ended_at' => null,
        ]);
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => [
            'employee_id' => $employee->id,
        ]);
    }
}

