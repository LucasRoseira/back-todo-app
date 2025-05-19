<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="Task management operations"
 * )
 */
class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get list of tasks",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter_type",
     *         in="query",
     *         required=false,
     *         description="Filter type (today, pending, overdue)",
     *         @OA\Schema(type="string", enum={"today", "pending", "overdue"})
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         description="Filter by title (partial match)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=false,
     *         description="Filter by description (partial match)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         required=false,
     *         description="Filter by priority",
     *         @OA\Schema(type="string", enum={"low", "medium", "high"})
     *     ),
     *     @OA\Parameter(
     *         name="due_date",
     *         in="query",
     *         required=false,
     *         description="Filter tasks due after or on this date",
     *         @OA\Schema(type="string", format="date", example="2024-08-10")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks"
     *     )
     * )
     */

    public function index(TaskRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 10;

        unset($filters['per_page']);

        $paginated = $this->taskService->getAllTasks($perPage, $filters);

        return response()->json($paginated);
    }

    // app/Http/Controllers/TaskController.php

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}/history",
     *     summary="Get status history for a specific task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task history returned successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TaskHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function history(Task $task): JsonResponse
    {
        $history = $this->taskService->getTaskHistory($task);

        return response()->json($history);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully"
     *     )
     * )
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        return $this->taskService->createTask($validated);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully"
     *     )
     * )
     */
    public function update(TaskUpdateRequest $request, Task $task, MailService $mailService)
    {
        $validated = $request->validated();

        $statusChanged = isset($validated['status']) && $validated['status'] !== $task->status;
        $responsibleChanged = isset($validated['responsible']) && $validated['responsible'] !== $task->responsible;

        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $updatedTask = $this->taskService->updateTask($task, $validated);

        if ($statusChanged || $responsibleChanged) {
            $mailService->sendTaskUpdatedNotification($updatedTask);
        }

        return response()->json($updatedTask);
    }


    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully"
     *     )
     * )
     */
    public function destroy($id, MailService $mailService)
    {
        $task = Task::findOrFail($id);
        $taskTitle = $task->title;

        $task->delete();

        $mailService->sendTaskDeletedEmail('user@example.com', $taskTitle);

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
