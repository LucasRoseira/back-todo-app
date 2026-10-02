<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Todo API",
 *     description="Categories and tasks API. Interactive docs are served at /api/documentation."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API host"
 * )
 *
 * @OA\Schema(
 *     schema="ErrorMessage",
 *
 *     @OA\Property(property="message", type="string", example="Resource not found.")
 * )
 *
 * @OA\Schema(
 *     schema="MessageResponse",
 *
 *     @OA\Property(property="message", type="string", example="Task deleted successfully")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *
 *         @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TaskPage",
 *
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
 *     @OA\Property(property="last_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="total", type="integer", example=8)
 * )
 *
 * @OA\Schema(
 *     schema="CategoryPage",
 *
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
 *     @OA\Property(property="last_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="total", type="integer", example=3)
 * )
 */
class OpenApi {}
