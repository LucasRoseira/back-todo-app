<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Task",
 *     required={"title", "status", "priority"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Complete project report"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         example="Finish the quarterly project report"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         example="pending"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="string",
 *         enum={"low", "medium", "high"},
 *         example="medium"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         example="2024-12-31"
 *     ),
 *     @OA\Property(
 *         property="completed_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         example="2024-07-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         nullable=true,
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         readOnly=true,
 *         example="2024-07-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         readOnly=true,
 *         example="2024-07-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="responsible_name",
 *         type="string",
 *         example="João Silva",
 *         description="Responsible name for the task"
 *     ),

 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'category_id',
        'responsible_name'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class);
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->category?->name;
    }

    public static function rules(bool $updating = false): array
    {
        $rules = [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'sometimes|date|after_or_equal:today',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'responsible_name' => 'sometimes|nullable|string',
        ];

        if ($updating) $rules['completed_at'] = 'sometimes|nullable|date';

        return $rules;
    }
}
