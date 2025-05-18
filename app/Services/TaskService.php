<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Interfaces\TaskServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskService implements TaskServiceInterface
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasks(int $perPage = 10, array $filters): LengthAwarePaginator
    {
        return $this->taskRepository->getAllTasks($perPage, $filters);
    }

    public function getTaskHistory(Task $task): Collection
    {
        return $this->taskRepository->getHistoryByTaskId($task->id);
    }

    public function createTask(array $data)
    {
        return $this->taskRepository->createTask($data);
    }

    public function updateTask(Task $task, array $data)
    {
        $oldStatus = $task->status;

        $updatedTask = $this->taskRepository->updateTask($task, $data);

        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            $task->statusHistory()->create([
                'status' => $data['status'],
                'changed_at' => now()
            ]);
        }

        return $updatedTask;
    }

    public function deleteTask(Task $task)
    {
        return $this->taskRepository->deleteTask($task);
    }
}
