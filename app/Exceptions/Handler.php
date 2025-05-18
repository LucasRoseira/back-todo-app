<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Erro de validação.',
            'errors' => $exception->errors(),
        ], 422);
    }

    public function render($request, Throwable $exception): Response
    {
        return response()->json([
            'message' => $exception instanceof ValidationException ? 'Erro de validação.' : 'Erro interno no servidor.',
            'errors' => $exception instanceof ValidationException
                ? $exception->errors()
                : (config('app.debug') ? $exception->getMessage() : null),
        ], $exception instanceof ValidationException ? 422 : 500);
    }
}
