<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskServiceInterface
{
    public function getAllTasks(int $perPage = 10, array $filters);
    public function getTaskHistory(Task $task): Collection;
    public function createTask(array $data);
    public function updateTask(Task $task, array $data);
    public function deleteTask(Task $task);
}
