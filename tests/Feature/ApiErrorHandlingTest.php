<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_routes_and_methods_return_json(): void
    {
        $this->getJson('/api/does-not-exist')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');

        $this->postJson('/api/tasks/1/history')
            ->assertStatus(405)
            ->assertJsonPath('message', 'Method not allowed.');
    }

    public function test_user_endpoint_requires_a_sanctum_token(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    public function test_cors_allows_the_nuxt_dev_origin(): void
    {
        $this->withHeader('Origin', 'http://localhost:3000')
            ->getJson('/api/categories')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    }

    public function test_seeders_create_demo_data(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'demo@example.com']);
        $this->assertDatabaseHas('categories', ['name' => 'Work']);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('tasks', 8);

        $this->getJson('/api/tasks?filter_type=today')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Doctor appointment');

        $this->getJson('/api/tasks?filter_type=overdue')
            ->assertOk()
            ->assertJsonPath('total', 2);
    }
}
