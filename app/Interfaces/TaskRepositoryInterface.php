<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function getAllTasks(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function loadForDisplay(Task $task): Task;

    public function getHistoryByTaskId(int $taskId): Collection;

    public function createTask(array $taskData): Task;

    public function updateTask(Task $task, array $newData): Task;

    public function deleteTask(Task $task): void;
}
