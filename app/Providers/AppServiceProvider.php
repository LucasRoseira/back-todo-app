<?php

namespace App\Providers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\TaskServiceInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\TaskRepository;
use App\Services\CategoryService;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }

    public function boot(): void
    {
        // Single records stay unwrapped so clients keep reading the model fields at the top level.
        JsonResource::withoutWrapping();

        // Missing eager loads fail in local and test runs instead of issuing hidden queries.
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
