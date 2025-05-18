<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="TaskHistory",
 *     required={"task_id", "status", "changed_at"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="task_id",
 *         type="integer",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         example="completed"
 *     ),
 *     @OA\Property(
 *         property="changed_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-07-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-07-01T12:00:00Z",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-07-01T12:00:00Z",
 *         readOnly=true
 *     )
 * )
 */
class TaskStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'status',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @OA\Property(
     *     property="task",
     *     ref="#/components/schemas/Task"
     * )
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}