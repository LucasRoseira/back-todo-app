<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="TaskUpdateRequest",
 *     type="object",
 *     title="Task Update Request",
 *     description="Partial payload for updating a task. Only fields that are sent are changed.",
 *
 *     @OA\Property(property="title", type="string", maxLength=255, example="Update presentation"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Review the slides for project X"),
 *     @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="high"),
 *     @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress"),
 *     @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2026-10-12"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=2),
 *     @OA\Property(property="responsible_name", type="string", nullable=true, example="Noah"),
 *     @OA\Property(property="responsible_email", type="string", format="email", nullable=true, example="noah@example.com")
 * )
 */
class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'responsible_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'responsible_email' => ['sometimes', 'nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'The title may not be longer than 255 characters.',
            'status.in' => 'Status must be pending, in_progress, or completed.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'due_date.date' => 'Due date must be a valid date.',
            'completed_at.date' => 'Completed at must be a valid date.',
            'category_id.exists' => 'The selected category does not exist.',
            'category_id.integer' => 'Category id must be a whole number.',
            'responsible_email.email' => 'Responsible email must be a valid email address.',
        ];
    }
}
