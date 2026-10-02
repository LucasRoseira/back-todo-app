<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     type="object",
 *     required={"name"},
 *
 *     @OA\Property(property="name", type="string", maxLength=255, example="Work"),
 *     @OA\Property(property="color", type="string", nullable=true, example="#3b82f6", description="Hex color, #RGB or #RRGGBB")
 * )
 */
class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        $uniqueName = Rule::unique('categories', 'name');
        if ($categoryId) {
            $uniqueName = $uniqueName->ignore($categoryId);
        }

        return [
            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
                $uniqueName,
            ],
            'color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'name.max' => 'The name may not be longer than 255 characters.',
            'color.regex' => 'Color must be a hex value like #3b82f6.',
        ];
    }
}
