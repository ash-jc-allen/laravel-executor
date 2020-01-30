<?php

namespace AshAllenDesign\LaravelExecutor\Tests\Unit;

use AshAllenDesign\LaravelExecutor\Facades\Executor;
use AshAllenDesign\LaravelExecutor\Providers\LaravelExecutorProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider.
     *
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelExecutorProvider::class];
    }

    /**
     * Load package alias.
     *
     * @param  Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'laravel-executor' => Executor::class,
        ];
    }
}
