<?php

namespace Database\Factories;

use App\Models\TodoType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'todo_type_id' => TodoType::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_completed' => false,
            'due_date' => now()->addDays(5),
        ];
    }
}