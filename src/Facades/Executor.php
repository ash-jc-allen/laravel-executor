<?php

namespace AshAllenDesign\LaravelExecutor\Facades;

use Illuminate\Support\Facades\Facade;

class Executor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-executor';
    }
}
