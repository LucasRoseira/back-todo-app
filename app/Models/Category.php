<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     required={"id", "name"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Work"),
 *     @OA\Property(property="color", type="string", nullable=true, example="#3b82f6"),
 *     @OA\Property(property="tasks_count", type="integer", example=4, description="Present when the category query counted tasks"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
