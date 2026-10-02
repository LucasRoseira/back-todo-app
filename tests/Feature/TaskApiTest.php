<?php

namespace Tests\Feature;

use App\Mail\TaskDeletedMail;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginator_shape_and_eager_loads_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Work']);
        Task::factory()->count(3)->create([
            'category_id' => $category->id,
            'priority' => 'low',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->getJson('/api/tasks?per_page=2');

        $response->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3)
            ->assertJsonPath('data.0.category.id', $category->id)
            ->assertJsonPath('data.0.category_name', 'Work')
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    ['id', 'title', 'status', 'priority', 'due_date', 'category_id', 'responsible_name'],
                ],
                'last_page',
                'per_page',
                'total',
            ]);

        $categoryQueries = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $sql) => str_contains($sql, 'categories'));

        $this->assertCount(1, $categoryQueries);
    }

    public function test_index_filters_by_category_today_and_overdue(): void
    {
        $work = Category::factory()->create();
        $home = Category::factory()->create();

        Task::factory()->create([
            'title' => 'Due today',
            'category_id' => $work->id,
            'status' => 'pending',
            'due_date' => now()->toDateString(),
        ]);
        Task::factory()->create([
            'title' => 'Overdue item',
            'category_id' => $work->id,
            'status' => 'in_progress',
            'due_date' => now()->subDay()->toDateString(),
        ]);
        Task::factory()->create([
            'title' => 'Done yesterday',
            'category_id' => $home->id,
            'status' => 'completed',
            'due_date' => now()->subDay()->toDateString(),
            'completed_at' => now()->subDay(),
        ]);

        $this->getJson('/api/tasks?filter_type=today')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Due today');

        $this->getJson('/api/tasks?filter_type=overdue')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Overdue item');

        $this->getJson('/api/tasks?category_id='.$home->id)
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Done yesterday');
    }

    public function test_index_orders_high_priority_before_low(): void
    {
        $category = Category::factory()->create();
        Task::factory()->create([
            'title' => 'Low task',
            'priority' => 'low',
            'category_id' => $category->id,
            'due_date' => now()->toDateString(),
        ]);
        Task::factory()->create([
            'title' => 'High task',
            'priority' => 'high',
            'category_id' => $category->id,
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'High task')
            ->assertJsonPath('data.1.title', 'Low task');
    }

    public function test_store_requires_a_title_and_creates_history(): void
    {
        $this->postJson('/api/tasks', [])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The given data was invalid.')
            ->assertJsonPath('errors.title.0', 'A task title is required.');

        $category = Category::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'title' => 'Write tests',
            'category_id' => $category->id,
            'responsible_name' => 'Noah',
            'responsible_email' => 'noah@example.com',
        ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'Write tests')
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('priority', 'medium')
            ->assertJsonPath('category_name', $category->name);

        $taskId = $response->json('id');

        $this->assertDatabaseHas('task_status_histories', [
            'task_id' => $taskId,
            'status' => 'pending',
        ]);

        $this->getJson("/api/tasks/{$taskId}/history")
            ->assertOk()
            ->assertJsonPath('0.status', 'pending');
    }

    public function test_store_rejects_a_past_due_date_and_unknown_category(): void
    {
        $this->postJson('/api/tasks', [
            'title' => 'Late',
            'due_date' => now()->subDay()->toDateString(),
        ])->assertUnprocessable()
            ->assertJsonPath('errors.due_date.0', 'Due date cannot be in the past.');

        $this->postJson('/api/tasks', [
            'title' => 'Missing category',
            'category_id' => 9999,
        ])->assertUnprocessable()
            ->assertJsonPath('errors.category_id.0', 'The selected category does not exist.');
    }

    public function test_update_records_status_history_and_completed_at(): void
    {
        Mail::fake();

        $task = Task::factory()->create([
            'status' => 'pending',
            'completed_at' => null,
            'responsible_email' => 'noah@example.com',
        ]);

        $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('status', 'in_progress')
            ->assertJsonPath('completed_at', null);

        $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('status', 'completed');

        $this->assertNotNull($task->fresh()->completed_at);
        $this->assertDatabaseHas('task_status_histories', [
            'task_id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_show_and_missing_task_use_json_errors(): void
    {
        $task = Task::factory()->create();

        $this->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('id', $task->id);

        $this->getJson('/api/tasks/9999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_delete_removes_the_task_and_notifies_the_responsible(): void
    {
        Mail::fake();

        $task = Task::factory()->create([
            'responsible_email' => 'noah@example.com',
        ]);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Task deleted successfully');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        Mail::assertSent(TaskDeletedMail::class);
    }
}
