<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_includes_task_counts_without_embedding_tasks(): void
    {
        $category = Category::factory()->create(['name' => 'Study', 'color' => '#f59e0b']);
        Task::factory()->count(2)->create(['category_id' => $category->id]);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.name', 'Study')
            ->assertJsonPath('data.0.tasks_count', 2)
            ->assertJsonMissingPath('data.0.tasks');
    }

    public function test_store_update_and_delete_keep_tasks_when_category_is_removed(): void
    {
        $this->postJson('/api/categories', [
            'name' => 'Errands',
            'color' => 'blue',
        ])->assertUnprocessable()
            ->assertJsonPath('errors.color.0', 'Color must be a hex value like #3b82f6.');

        $created = $this->postJson('/api/categories', [
            'name' => 'Errands',
            'color' => '#10b981',
        ])->assertCreated()
            ->assertJsonPath('name', 'Errands')
            ->assertJsonPath('tasks_count', 0);

        $this->postJson('/api/categories', [
            'name' => 'Errands',
        ])->assertUnprocessable()
            ->assertJsonPath('errors.name.0', 'A category with this name already exists.');

        $categoryId = $created->json('id');
        $task = Task::factory()->create(['category_id' => $categoryId]);

        $this->putJson("/api/categories/{$categoryId}", [
            'name' => 'Errands',
            'color' => '#000',
        ])->assertOk()
            ->assertJsonPath('color', '#000');

        $this->deleteJson("/api/categories/{$categoryId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
        $this->assertNull($task->fresh()->category_id);
    }
}
