<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'payload' => $request->except(['password', 'password_confirmation']),
        ];

        Log::info('Request recebida:', $logData);
        info('Request recebida:', $logData);

        return $next($request);
    }
}
