<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskServiceInterface
{
    public function getAllTasks(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function getTask(Task $task): Task;

    public function getTaskHistory(Task $task): Collection;

    public function createTask(array $data): Task;

    public function updateTask(Task $task, array $data): Task;

    public function deleteTask(Task $task): void;
}
