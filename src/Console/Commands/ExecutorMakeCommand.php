<?php

namespace AshAllenDesign\LaravelExecutor\Console\Commands;

use AshAllenDesign\LaravelExecutor\Exceptions\ExecutorCommandException;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
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
    protected function getStub(): string
    {
        $stub = '/Stubs/ExecutorDefinition.stub';

        return __DIR__.$stub;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return string
     */
    protected function alreadyExists($rawName): string
    {
        return class_exists($this->rootNamespace().'Executor\\'.$rawName);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Executor';
    }

    /**
     * Get the default namespace where the command
     * belongs.
     *
     * @return string
     */
    protected function defaultCommandNamespace(): string
    {
        return 'App\Console\Commands\\';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws ExecutorCommandException
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    protected function buildClass($name): string
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
    protected function getOptions(): array
    {
        return [
            ['command', 'c', InputOption::VALUE_NONE, 'Generate a command for running the executor class.'],
        ];
    }

    /**
     * Create a boilerplate command that can be used to
     * run the executor class in the console.
     *
     * @throws ExecutorCommandException
     * @throws ReflectionException
     */
    protected function createExecutorCommandClass(): void
    {
        $commandClassName = 'Run'.$this->argument('name').'Executor';
        $commandSignature = 'executor:'.Str::kebab($this->argument('name'));
        $commandFullClassName = $this->laravel->getNamespace().'Console\Commands\\'.$commandClassName;

        $this->call('make:command', [
            'name'      => $commandClassName,
            '--command' => $commandSignature
        ]);

        $commandFilePath = (new ReflectionClass($commandFullClassName))->getFileName();

        if (!File::exists($commandFilePath) || !File::isReadable($commandFilePath) || !File::isWritable($commandFilePath)) {
            throw new ExecutorCommandException('The command file either does not exist or cannot be written to.');
        }

        $this->replaceCommandClassContents($commandFilePath);
    }

    /**
     * Replace the body of the command class.
     *
     * @param  string  $commandFilePath
     */
    private function replaceCommandClassContents(string $commandFilePath): void
    {
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
    private function replaceDescription(string $fileContents): string
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
        $replaceWith = "(new {$executorClass}())->run(true);";

        return str_replace($find, $replaceWith, $fileContents);
    }
}
