<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
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

    public function render($request, Throwable $e): Response
    {
        if ($this->wantsJson($request)) {
            return $this->renderJson($this->prepareException($this->normalize($e)));
        }

        return parent::render($request, $e);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function normalize(Throwable $e): Throwable
    {
        if ($e instanceof ModelNotFoundException) {
            return new NotFoundHttpException('Resource not found.', $e);
        }

        return $e;
    }

    private function renderJson(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($e instanceof UniqueConstraintViolationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => ['A record with these values already exists.'],
                ],
            ], 422);
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

        $message = match ($status) {
            403 => 'This action is unauthorized.',
            404 => 'Resource not found.',
            405 => 'Method not allowed.',
            419 => 'Page expired.',
            default => $status >= 500 ? 'Server error.' : ($e->getMessage() ?: 'Request could not be completed.'),
        };

        $payload = ['message' => $message];

        if (config('app.debug') && $status >= 500) {
            $payload['exception'] = $e::class;
            $payload['debug'] = $e->getMessage();
        }

        return response()->json($payload, $status);
    }
}
