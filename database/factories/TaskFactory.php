<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'in_progress', 'completed']);

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'due_date' => fake()->dateTimeBetween('-3 days', '+10 days')->format('Y-m-d'),
            'completed_at' => $status === 'completed' ? now() : null,
            'category_id' => Category::factory(),
            'responsible_name' => fake()->name(),
            'responsible_email' => fake()->safeEmail(),
        ];
    }
}
