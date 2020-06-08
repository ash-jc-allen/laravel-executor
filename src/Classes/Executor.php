<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

class Executor
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
     * Run an Artisan command and return the output.
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
     * Run a command on the system.
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
     * should be run.
     *
     * @return array
     */
    public function commandsToRun(): array
    {
        return $this->commandsToRun;
    }
}
