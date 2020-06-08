<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

use Symfony\Component\Process\Process;

abstract class ExecutorDefinition
{
    /**
     * The Executor class that will run the commands.
     *
     * @var Executor
     */
    protected $executor;

    /**
     * ExecutorDefinition constructor.
     *
     * @param  Executor|null  $executor
     */
    public function __construct(Executor $executor = null)
    {
        $this->executor = $executor ?? new Executor();
    }

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
        $this->executor->resetOutput();

        $this->definition();

        foreach ($this->executor->commandsToRun() as $command) {
            $commandArray = explode(' ', $command);

            $process = new Process($commandArray);

            $process->run(function ($type, $buffer) use ($consoleMode) {
                if ($consoleMode) {
                    echo $buffer;
                }
            });

            $this->executor->setOutput($this->executor->getOutput().$process->getOutput());
        }

        return $this->executor->getOutput();
    }

    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     *
     * @return Executor
     */
    abstract public function definition(): Executor;
}
