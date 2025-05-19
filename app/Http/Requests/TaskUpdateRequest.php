<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="TaskUpdateRequest",
 *     type="object",
 *     title="Task Update Request",
 *     description="Schema for updating a task",
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Update presentation"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Review the slides for project X"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="integer",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "completed"},
 *         example="completed"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         example="2024-08-12"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         example=2
 *     ),
 * 
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
            'status' => 'sometimes|in:pending,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'sometimes|date|nullable',
            'completed_at' => 'sometimes|date|nullable',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'responsible_name' => 'sometimes|string|max:255',
        ];
    }
}
