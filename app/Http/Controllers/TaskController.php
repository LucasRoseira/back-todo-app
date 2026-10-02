<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskStatusHistoryResource;
use App\Interfaces\TaskServiceInterface;
use App\Models\Task;
use App\Support\PaginatedResource;
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
    public function __construct(private readonly TaskServiceInterface $taskService) {}

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get a paginated list of tasks",
     *     tags={"Tasks"},
     *
     *     @OA\Parameter(name="per_page", in="query", required=false, description="Items per page (1-100)", @OA\Schema(type="integer", example=10)),
     *     @OA\Parameter(name="filter_type", in="query", required=false, description="today (due today), pending, or overdue (due before today and not completed)", @OA\Schema(type="string", enum={"today", "pending", "overdue"})),
     *     @OA\Parameter(name="title", in="query", required=false, description="Partial title match", @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", required=false, description="Partial description match", @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})),
     *     @OA\Parameter(name="priority", in="query", required=false, @OA\Schema(type="string", enum={"low", "medium", "high"})),
     *     @OA\Parameter(name="due_date", in="query", required=false, description="Tasks due on or after this date", @OA\Schema(type="string", format="date", example="2026-10-02")),
     *     @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="responsible_name", in="query", required=false, description="Partial responsible name match", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Paginated tasks", @OA\JsonContent(ref="#/components/schemas/TaskPage")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexTaskRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);
        unset($filters['per_page']);

        $paginated = $this->taskService->getAllTasks($perPage, $filters);

        return response()->json(PaginatedResource::make($paginated, TaskResource::class));
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a task",
     *     tags={"Tasks"},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TaskRequest")),
     *
     *     @OA\Response(response=201, description="Task created", @OA\JsonContent(ref="#/components/schemas/Task")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(TaskRequest $request): TaskResource
    {
        return new TaskResource($this->taskService->createTask($request->validated()));
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}",
     *     summary="Get a task",
     *     tags={"Tasks"},
     *
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Task", @OA\JsonContent(ref="#/components/schemas/Task")),
     *     @OA\Response(response=404, description="Task not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage"))
     * )
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($this->taskService->getTask($task));
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TaskUpdateRequest")),
     *
     *     @OA\Response(response=200, description="Task updated", @OA\JsonContent(ref="#/components/schemas/Task")),
     *     @OA\Response(response=404, description="Task not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(TaskUpdateRequest $request, Task $task): TaskResource
    {
        return new TaskResource($this->taskService->updateTask($task, $request->validated()));
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Task deleted", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *     @OA\Response(response=404, description="Task not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage"))
     * )
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}/history",
     *     summary="Get status history for a task",
     *     tags={"Tasks"},
     *
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Newest status change first",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/TaskHistory"))
     *     ),
     *
     *     @OA\Response(response=404, description="Task not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage"))
     * )
     */
    public function history(Task $task): JsonResponse
    {
        $history = $this->taskService->getTaskHistory($task);

        return TaskStatusHistoryResource::collection($history)->response();
    }
}
