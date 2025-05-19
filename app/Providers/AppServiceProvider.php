<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Repositories\TaskRepository;
use App\Repositories\CategoryRepository;
use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;



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
            CategoryRepositoryInterface::class,
            CategoryRepository::class
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
