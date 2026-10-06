<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        if (Task::query()->exists()) {
            return;
        }

        $work = Category::query()->where('name', 'Work')->firstOrFail();
        $personal = Category::query()->where('name', 'Personal')->firstOrFail();
        $study = Category::query()->where('name', 'Study')->firstOrFail();

        $tasks = [
            [
                'title' => 'Finish project proposal',
                'description' => 'Draft and submit the project proposal document.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7)->toDateString(),
                'category_id' => $work->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending'],
            ],
            [
                'title' => 'Team meeting',
                'description' => 'Discuss Q3 goals and planning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2)->toDateString(),
                'category_id' => $work->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending', 'in_progress'],
            ],
            [
                'title' => 'Submit tax documents',
                'description' => 'Collect and send all required tax documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10)->toDateString(),
                'category_id' => $work->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending'],
            ],
            [
                'title' => 'Renew passport',
                'description' => 'Book an appointment and gather documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->subDays(3)->toDateString(),
                'category_id' => $personal->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending'],
            ],
            [
                'title' => 'Doctor appointment',
                'description' => 'Annual check-up.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->toDateString(),
                'category_id' => $personal->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending'],
            ],
            [
                'title' => 'Grocery shopping',
                'description' => 'Buy fruits, vegetables, and essentials.',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDay()->toDateString(),
                'completed_at' => now()->subDay(),
                'category_id' => $personal->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending', 'completed'],
            ],
            [
                'title' => 'Read Laravel docs',
                'description' => 'Review Eloquent relationships and API resources.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDay()->toDateString(),
                'category_id' => $study->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending', 'in_progress'],
            ],
            [
                'title' => 'Pay invoice',
                'description' => 'Pay the overdue vendor invoice.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->subDay()->toDateString(),
                'category_id' => $work->id,
                'responsible_name' => 'Noah',
                'responsible_email' => 'noah@example.com',
                'history' => ['pending', 'in_progress'],
            ],
        ];

        foreach ($tasks as $index => $payload) {
            $history = $payload['history'];
            unset($payload['history']);

            $task = Task::query()->create($payload);

            foreach ($history as $step => $status) {
                $task->statusHistory()->create([
                    'status' => $status,
                    'changed_at' => now()->subDays(count($history) - $step + $index),
                ]);
            }
        }
    }
}
