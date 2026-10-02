<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->toDateString(),
            'completed_at' => $this->completed_at?->toJSON(),
            'category_id' => $this->category_id,
            'category_name' => $this->when(
                $this->relationLoaded('category'),
                fn () => $this->category?->name
            ),
            'category' => $this->when(
                $this->relationLoaded('category') && $this->category !== null,
                fn () => new CategoryResource($this->category)
            ),
            'responsible_name' => $this->responsible_name,
            'responsible_email' => $this->responsible_email,
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
