<?php

namespace AshAllenDesign\LaravelExecutor\Console\Commands;

use AshAllenDesign\LaravelExecutor\Exceptions\ExecutorCommandException;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

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

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws ExecutorCommandException
     * @throws FileNotFoundException
     * @throws \ReflectionException
     */
    protected function buildClass($name)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $name)) {
            throw new InvalidArgumentException('Executor class name contains invalid characters.');
        }

        if ($this->option('command')) {
            $this->createExecutorCommandClass();
        }

        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', 'c', InputOption::VALUE_NONE, 'Generate a command for running the executor class.'],
        ];
    }

    /**
     * @throws ExecutorCommandException
     * @throws \ReflectionException
     */
    protected function createExecutorCommandClass()
    {
        $commandClassName = 'Run'.$this->argument('name').'Executor';
        $commandSignature = 'executor:'.Str::kebab($this->argument('name'));

        if (!class_exists($commandClassName)) {
            $this->call('make:command', [
                'name'      => $commandClassName,
                '--command' => $commandSignature
            ]);
        }

        $commandFullClassName = $this->rootNamespace().'Console\Commands\\'.$commandClassName;

        $commandFilePath = (new \ReflectionClass($commandFullClassName))->getFileName();

        if (!File::exists($commandFilePath) && File::isReadable($commandFilePath) && File::isWritable($commandFilePath)) {
            throw new ExecutorCommandException('The command file either does not exist or cannot be written to.');
        }

        $fileContents = File::get($commandFilePath);

        $fileContents = $this->replaceDescription($fileContents);
        $fileContents = $this->replaceHandleBody($fileContents);

        File::put($commandFilePath, $fileContents);
    }

    /**
     * Replace the description of the command that was
     * just created for running the executor class.
     *
     * @param  string  $fileContents
     * @return string
     */
    protected function replaceDescription(string $fileContents): string
    {
        $find = 'protected $description = \'Command description\';';
        $replaceWith = 'protected $description = \'Run the '.$this->argument('name').' executor class.\';';

        return str_replace($find, $replaceWith, $fileContents);
    }

    /**
     * Replace the handle() method body to run the
     * executor file.
     *
     * @param  string  $fileContents
     * @return string
     */
    private function replaceHandleBody(string $fileContents): string
    {
        // Remove the double quotes from the class path.
        $executorClass = str_replace(
            '\\\\',
            '\\',
            "\\".$this->getDefaultNamespace($this->rootNamespace()).'\\'.$this->argument('name')
        );

        $find = "//";
        $replaceWith = "(new {$executorClass}())->run();";

        return str_replace($find, $replaceWith, $fileContents);
    }
}
