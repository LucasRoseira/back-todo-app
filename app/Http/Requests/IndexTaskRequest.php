<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'filter_type' => ['sometimes', 'in:today,pending,overdue'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'responsible_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page must be a whole number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'You can request at most 100 items per page.',
            'filter_type.in' => 'Filter type must be today, pending, or overdue.',
            'status.in' => 'Status must be pending, in_progress, or completed.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'due_date.date' => 'Due date must be a valid date.',
            'category_id.exists' => 'The selected category does not exist.',
            'category_id.integer' => 'Category id must be a whole number.',
        ];
    }
}
