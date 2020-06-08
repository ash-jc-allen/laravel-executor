<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class Executor
{
    /**
     * The output from running any commands.
     *
     * @var string
     */
    private static $output = '';

    /**
     * Run an Artisan command and return the output.
     *
     * @param  string  $command
     * @return string
     */
    public function runArtisan(string $command): string
    {
        Artisan::call($command);

        $this->setOutput($this->getOutput().Artisan::output());

        return Artisan::output();
    }

    /**
     * Run a command on the system.
     *
     * @param  string  $command
     * @return string
     */
    public function runExternal(string $command): string
    {
        $commandArray = explode(' ', $command);

        $process = new Process($commandArray);
        $process->run();

        $this->setOutput($this->getOutput().$process->getOutput());

        return $process->getOutput();
    }

    /**
     * Get the output from the commands that have been ran.
     * We can use this for displaying to the console.
     *
     * @return string
     */
    public function getOutput()
    {
        return self::$output;
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
        self::$output = $output;

        return $this;
    }
}
