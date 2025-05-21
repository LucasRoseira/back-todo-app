<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{

    public function getAllTasks(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
         $query = Task::with('category');

        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['description'])) {
            $query->where('description', 'like', '%' . $filters['description'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', '>=', $filters['due_date']);
        }

        if (isset($filters['responsible_name'])) {
            $query->where('responsible_name', 'like', '%' . $filters['responsible_name'] . '%');
        }

        if (isset($filters['filter_type'])) {
            switch ($filters['filter_type']) {
                case 'today':
                    $query->whereDate('due_date', today());
                    break;

                case 'pending':
                    $query->where('status', 'pending');
                    break;

                case 'overdue':
                    $query->where('due_date', '<', now())
                        ->where('status', '!=', 'completed');
                    break;
            }
        }

        return $query->orderBy('priority')->paginate($perPage);
    }

    public function getHistoryByTaskId(int $taskId): Collection
    {
        return TaskStatusHistory::where('task_id', $taskId)
            ->orderByDesc('changed_at')
            ->get();
    }

    public function createTask(array $taskData): Task
    {
        return Task::create($taskData);
    }

    public function updateTask(Task $task, array $newData): Task
    {
        $task->update($newData);
        return $task;
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
        return true;
    }
}
