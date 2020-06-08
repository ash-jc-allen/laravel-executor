<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

use Symfony\Component\Process\Process;

abstract class Executor
{
    /**
     * The output from running any commands.
     *
     * @var string
     */
    private $output = '';

    /**
     * An array containing all of the commands that should
     * be run.
     *
     * @var array
     */
    private $commandsToRun = [];

    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     *
     * @return Executor
     */
    abstract public function definition(): Executor;

    /**
     * Run the commands defined that are in the executor
     * definition. If $consoleMode is set to true, the
     * command's output will displayed in realtime.
     * This is useful for long running commands.
     *
     * @param  bool  $consoleMode
     * @return string
     */
    public function run(bool $consoleMode = false): string
    {
        $this->resetOutput();

        foreach ($this->commandsToRun() as $command) {
            if ($command instanceof \Closure) {
                $this->handleClosure($command, $consoleMode);

                continue;
            }

            $this->handleConsoleCommand($command, $consoleMode);
        }

        return $this->getOutput();
    }

    /**
     * Handle the running of a closure.
     *
     * @param  \Closure  $closureToRun
     * @param  bool  $consoleMode
     */
    private function handleClosure(\Closure $closureToRun, bool $consoleMode): void
    {
        $output = call_user_func($closureToRun);

        if ($consoleMode) {
            echo $output;
        }

        $this->setOutput($this->getOutput().$output);
    }

    /**
     * Handle the running of a console command.
     *
     * @param  string  $commandToRun
     * @param  bool  $consoleMode
     */
    private function handleConsoleCommand(string $commandToRun, bool $consoleMode): void
    {
        $process = new Process(explode(' ', $commandToRun));

        $process->run(function ($type, $buffer) use ($consoleMode) {
            if ($consoleMode) {
                echo $buffer;
            }
        });

        $this->setOutput($this->getOutput().$process->getOutput());
    }

    /**
     * Add an Artisan command to the queue of items that
     * should be executed.
     *
     * @param  string  $command
     * @return $this
     */
    public function runArtisan(string $command): self
    {
        $this->commandsToRun[] = 'php artisan '.$command;

        return $this;
    }

    /**
     * Add a command (external to Laravel) to the queue of
     * items that should be executed.
     *
     * @param  string  $command
     * @return $this
     */
    public function runExternal(string $command): self
    {
        $this->commandsToRun[] = $command;

        return $this;
    }

    /**
     * Add a closure to the queue of items that should be
     * executed.
     *
     * @param  \Closure  $closure
     * @return $this
     */
    public function runClosure(\Closure $closure): self
    {
        $this->commandsToRun[] = $closure;

        return $this;
    }

    /**
     * Get the output from the commands that have been ran.
     * We can use this for displaying to the console.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Reset the output. This is useful for running after
     * the definitions have been ran and we don't need
     * the output anymore.
     *
     * @return $this
     */
    public function resetOutput()
    {
        return $this->setOutput('');
    }

    /**
     * Set the output from the commands. This is useful
     * for after we have run a command and want to
     * store the output of it.
     *
     * @param  string  $output
     * @return $this
     */
    public function setOutput(string $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Return the array containing the commands that
     * should be run. We do this by calling the
     * 'definition' method specified in the
     * Executor child class.
     *
     * @return array
     */
    public function commandsToRun(): array
    {
        $this->definition();

        return $this->commandsToRun;
    }
}
