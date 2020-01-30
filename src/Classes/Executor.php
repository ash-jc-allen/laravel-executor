<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class Executor
{
    /**
     * Run an Artisan command and return the output.
     *
     * @param  string  $command
     * @return string
     */
    public function runArtisan(string $command): string
    {
        Artisan::call($command);

        return Artisan::output();
    }

    /**
     * Run a command on the system.
     *
     * @param  array  $command
     * @return string
     */
    public function runExternal(array $command): string
    {
        $process = new Process($command);
        $process->run();

        return $process->getOutput();
    }
}
