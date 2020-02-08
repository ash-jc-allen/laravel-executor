<?php

namespace AshAllenDesign\LaravelExecutor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string runArtisan(string $command)
 * @method static string runExternal(array $command)
 * @method static string getOutput()
 * @method static \AshAllenDesign\LaravelExecutor\Classes\Executor resetOutput()
 * @method static \AshAllenDesign\LaravelExecutor\Classes\Executor setOutput(string $output)
 *
 * @see \AshAllenDesign\LaravelExecutor\Classes\Executor
 */
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
