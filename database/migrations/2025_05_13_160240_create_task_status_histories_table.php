<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed']);
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index(['task_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_histories');
    }
};
