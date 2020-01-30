<?php

namespace AshAllenDesign\LaravelExecutor\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ExecutorMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:executor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new executor definition class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Executor';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/Stubs/ExecutorDefinition.stub';

        return __DIR__.$stub;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($this->rootNamespace().'Executor\\'.$rawName);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Executor';
    }
}
