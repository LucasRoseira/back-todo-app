<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="TaskRequest",
 *     type="object",
 *     required={"title"},
 *     title="Task Request",
 *     description="Payload for creating a task. Status defaults to pending and priority defaults to medium when omitted.",
 *
 *     @OA\Property(property="title", type="string", maxLength=255, example="Finish report"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Complete the weekly report by Friday"),
 *     @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="medium"),
 *     @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending"),
 *     @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2026-10-10"),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="responsible_name", type="string", nullable=true, example="Noah"),
 *     @OA\Property(property="responsible_email", type="string", format="email", nullable=true, example="noah@example.com")
 * )
 */
class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'pending'),
            'priority' => $this->input('priority', 'medium'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'responsible_name' => ['nullable', 'string', 'max:255'],
            'responsible_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A task title is required.',
            'title.max' => 'The title may not be longer than 255 characters.',
            'status.in' => 'Status must be pending, in_progress, or completed.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'due_date.date' => 'Due date must be a valid date.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'category_id.exists' => 'The selected category does not exist.',
            'category_id.integer' => 'Category id must be a whole number.',
            'responsible_email.email' => 'Responsible email must be a valid email address.',
        ];
    }
}
