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
        $query = Task::query()->with('category');

        if ($this->filled($filters, 'title')) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }

        if ($this->filled($filters, 'description')) {
            $query->where('description', 'like', '%'.$filters['description'].'%');
        }

        if ($this->filled($filters, 'status')) {
            $query->where('status', $filters['status']);
        }

        if ($this->filled($filters, 'priority')) {
            $query->where('priority', $filters['priority']);
        }

        if ($this->filled($filters, 'due_date')) {
            $query->whereDate('due_date', '>=', $filters['due_date']);
        }

        if ($this->filled($filters, 'category_id')) {
            $query->where('category_id', $filters['category_id']);
        }

        if ($this->filled($filters, 'responsible_name')) {
            $query->where('responsible_name', 'like', '%'.$filters['responsible_name'].'%');
        }

        if ($this->filled($filters, 'filter_type')) {
            match ($filters['filter_type']) {
                'today' => $query->whereDate('due_date', today()),
                'pending' => $query->where('status', 'pending'),
                // Before today, so a task due today stays in the "today" list for the whole day.
                'overdue' => $query->whereDate('due_date', '<', today())
                    ->where('status', '!=', 'completed'),
                default => null,
            };
        }

        return $query
            ->orderByRaw("case priority when 'high' then 1 when 'medium' then 2 when 'low' then 3 else 4 end")
            ->orderBy('due_date')
            ->paginate($perPage);
    }

    public function loadForDisplay(Task $task): Task
    {
        return $task->load('category');
    }

    public function getHistoryByTaskId(int $taskId): Collection
    {
        return TaskStatusHistory::query()
            ->where('task_id', $taskId)
            ->orderByDesc('changed_at')
            ->orderByDesc('id')
            ->get();
    }

    public function createTask(array $taskData): Task
    {
        return Task::query()->create($taskData);
    }

    public function updateTask(Task $task, array $newData): Task
    {
        $task->update($newData);

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    private function filled(array $filters, string $key): bool
    {
        return array_key_exists($key, $filters) && $filters[$key] !== null && $filters[$key] !== '';
    }
}
