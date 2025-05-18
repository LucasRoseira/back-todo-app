<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Repositories\TaskRepository;
use App\Repositories\CategoryRepository;
use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Repositories\TaskStatusHistoryRepository;
use App\Interfaces\TaskStatusHistoryRepositoryInterface;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class,
        );
        $this->app->bind(
            CategoryRepository::class,
            CategoryRepositoryInterface::class
        );
        $this->app->bind(
            TaskStatusHistoryRepositoryInterface::class,
            TaskStatusHistoryRepository::class
        );
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
