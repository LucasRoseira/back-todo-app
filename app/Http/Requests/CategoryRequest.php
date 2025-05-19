<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Technology")
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
        return [
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:7',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
