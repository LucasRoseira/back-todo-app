<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'payload' => $request->except(['password', 'password_confirmation']),
        ];

        Log::info('Incoming request', $logData);

        return $next($request);
    }
}
