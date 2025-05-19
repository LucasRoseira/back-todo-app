<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="TaskRequest",
 *     type="object",
 *     required={"title", "description", "priority", "status"},
 *     title="Task Request",
 *     description="Schema for creating a new task",
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Finish report"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Complete the weekly report by Friday"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "completed"},
 *         example="pending"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         example="2024-08-10"
 *     ),
 *      @OA\Property(
 *         property="filter_type",
 *         type="string",
 *         enum={"today", "pending", "overdue"},
 *         example="pending"
 *     ),
 *     @OA\Property(
 *         property="responsible_name",
 *         type="string",
 *         example="João Silva",
 *         description="Nome do responsável pela tarefa"
 *     ),
 * )
 */

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'sometimes|date|after_or_equal:today',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'filter_type' => 'sometimes|in:today,pending,overdue',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'responsible_name' => 'sometimes|string|max:255',
        ];
    }
}
