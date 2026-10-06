<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page must be a whole number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'You can request at most 100 items per page.',
            'name.max' => 'The name filter may not be longer than 255 characters.',
        ];
    }
}
