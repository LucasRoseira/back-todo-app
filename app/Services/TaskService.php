<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly MailService $mailService,
    ) {}

    public function getAllTasks(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->getAllTasks($perPage, $filters);
    }

    public function getTask(Task $task): Task
    {
        return $this->taskRepository->loadForDisplay($task);
    }

    public function getTaskHistory(Task $task): Collection
    {
        return $this->taskRepository->getHistoryByTaskId($task->id);
    }

    public function createTask(array $data): Task
    {
        $task = $this->taskRepository->createTask($data);

        $task->statusHistory()->create([
            'status' => $task->status,
            'changed_at' => now(),
        ]);

        return $this->taskRepository->loadForDisplay($task);
    }

    public function updateTask(Task $task, array $data): Task
    {
        $previousStatus = $task->status;
        $previousResponsibleName = $task->responsible_name;
        $previousResponsibleEmail = $task->responsible_email;

        if (array_key_exists('status', $data)) {
            if ($data['status'] === 'completed' && empty($data['completed_at'])) {
                $data['completed_at'] = now();
            }

            if ($data['status'] !== 'completed') {
                $data['completed_at'] = null;
            }
        }

        $updatedTask = $this->taskRepository->updateTask($task, $data);

        $statusChanged = array_key_exists('status', $data) && $data['status'] !== $previousStatus;

        if ($statusChanged) {
            $updatedTask->statusHistory()->create([
                'status' => $updatedTask->status,
                'changed_at' => now(),
            ]);
        }

        $responsibleChanged = (array_key_exists('responsible_name', $data) && $data['responsible_name'] !== $previousResponsibleName)
            || (array_key_exists('responsible_email', $data) && $data['responsible_email'] !== $previousResponsibleEmail);

        if ($statusChanged || $responsibleChanged) {
            $this->mailService->notifyTaskUpdated($updatedTask);
        }

        return $this->taskRepository->loadForDisplay($updatedTask);
    }

    public function deleteTask(Task $task): void
    {
        $this->mailService->notifyTaskDeleted($task);
        $this->taskRepository->deleteTask($task);
    }
}
