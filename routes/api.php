<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| Task and category routes are public so a Nuxt client can call them without
| a session cookie. GET /api/user is the authenticated Sanctum probe.
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks/{task}/history', [TaskController::class, 'history'])->name('tasks.history');
Route::apiResource('tasks', TaskController::class);
Route::apiResource('categories', CategoryController::class);
