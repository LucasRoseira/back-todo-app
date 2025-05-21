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
            $table->string('responsible_name')->nullable();
            $table->timestamps();
        });

        DB::table('categories')->insert([
            ['name' => 'Work', 'color' => '#3b82f6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal', 'color' => '#10b981', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Study', 'color' => '#f59e0b', 'created_at' => now(), 'updated_at' => now()],
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
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(5),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Finish project proposal',
                'description' => 'Draft and submit the project proposal document.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Grocery shopping',
                'description' => 'Buy fruits, vegetables, and essentials.',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDays(1),
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Team meeting',
                'description' => 'Discuss Q3 goals and planning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2),
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Doctor appointment',
                'description' => 'Annual check-up at 10 AM.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(4),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Submit tax documents',
                'description' => 'Collect and send all required tax documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

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
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(5),
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Finish project proposal',
                'description' => 'Draft and submit the project proposal document.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Grocery shopping',
                'description' => 'Buy fruits, vegetables, and essentials.',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDays(1),
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'
            ],
            [
                'title' => 'Team meeting',
                'description' => 'Discuss Q3 goals and planning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Doctor appointment',
                'description' => 'Annual check-up at 10 AM.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(4),
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

            ],
            [
                'title' => 'Submit tax documents',
                'description' => 'Collect and send all required tax documents.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10),
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'responsible_name' => 'Noah'

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

        DB::table('task_status_histories')->insert([
            [
                'task_id' => 1,
                'status' => 'pending',
                'changed_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'task_id' => 1,
                'status' => 'in_progress',
                'changed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'task_id' => 1,
                'status' => 'completed',
                'changed_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'task_id' => 2,
                'status' => 'pending',
                'changed_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'task_id' => 2,
                'status' => 'in_progress',
                'changed_at' => now()->subDays(4),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'task_id' => 3,
                'status' => 'pending',
                'changed_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('task_status_histories');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('categories');
    }
};
