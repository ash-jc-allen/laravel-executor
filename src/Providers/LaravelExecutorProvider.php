<?php

namespace AshAllenDesign\LaravelExecutor\Providers;

use AshAllenDesign\LaravelExecutor\Classes\Executor;
use Illuminate\Support\ServiceProvider;

class LaravelExecutorProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->alias(Executor::class, 'laravel-executor');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
