<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->index()->default('medium');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->timestamps();
        });

        DB::table('categories')->insert([
            ['name' => 'Trabalho', 'color' => '#3b82f6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pessoal', 'color' => '#10b981', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Estudo', 'color' => '#f59e0b', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $workCategoryId = DB::table('categories')->where('name', 'Work')->value('id');
        $personalCategoryId = DB::table('categories')->where('name', 'Grocery Store')->value('id');

        DB::table('tasks')->insert([
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(3),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(5),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Finish project proposal',
                'description' => 'Draft and submit the project proposal document.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Grocery shopping',
                'description' => 'Buy fruits, vegetables, and essentials.',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDays(1),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Team meeting',
                'description' => 'Discuss Q3 goals and planning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Doctor appointment',
                'description' => 'Annual check-up at 10 AM.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(4),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Submit tax documents',
                'description' => 'Collect and send all required tax documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(3),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(5),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Finish project proposal',
                'description' => 'Draft and submit the project proposal document.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Grocery shopping',
                'description' => 'Buy fruits, vegetables, and essentials.',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDays(1),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Team meeting',
                'description' => 'Discuss Q3 goals and planning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Doctor appointment',
                'description' => 'Annual check-up at 10 AM.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(4),
                'category_id' => $personalCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Submit tax documents',
                'description' => 'Collect and send all required tax documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10),
                'category_id' => $workCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        Schema::create('task_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed']);
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index('task_id');
            $table->index('changed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_status_histories');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('categories');
    }
};
