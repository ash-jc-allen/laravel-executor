<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

use AshAllenDesign\LaravelExecutor\Traits\DesktopNotifications;
use Closure;
use GuzzleHttp\Client;
use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\NotifierFactory;
use Symfony\Component\Process\Process;

abstract class Executor
{
    use DesktopNotifications;

    /**
     * The output from running any commands.
     *
     * @var string
     */
    private $output = '';

    /**
     * The notifier used for generating the desktop
     * notifications.
     *
     * @var Notifier|NotifierFactory
     */
    private $notifier;

    /**
     * The Guzzle client used for pinging URLs.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Executor constructor.
     *
     * @param  NotifierFactory|null  $notifierFactory
     * @param  Client|null  $httpClient
     */
    public function __construct(NotifierFactory $notifierFactory = null, Client $httpClient = null)
    {
        $this->notifier = $notifierFactory ?? NotifierFactory::create();
        $this->httpClient = $httpClient ?? new Client();
    }

    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     *
     * @return Executor
     */
    abstract public function run(): self;

    /**
     * Add an Artisan command to the queue of items that
     * should be executed.
     *
     * @param string $command
     * @param bool $isInteractive
     * @return $this
     */
    public function runArtisan(string $command, bool $isInteractive = false): self
    {
        $command = 'php artisan '.$command;

        $this->runCommand($command, $isInteractive);

        return $this;
    }

    /**
     * Add a command (external to Laravel) to the queue of
     * items that should be executed.
     *
     * @param string $command
     * @param bool $isInteractive
     * @return $this
     */
    public function runExternal(string $command, bool $isInteractive = false): self
    {
        $this->runCommand($command, $isInteractive);

        return $this;
    }

    /**
     * Add a closure to the queue of items that should be
     * executed.
     *
     * @param  Closure  $closureToRun
     * @return $this
     */
    public function runClosure(Closure $closureToRun): self
    {
        $output = call_user_func($closureToRun);

        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            echo $output;
        }

        $this->setOutput($this->getOutput().$output);

        return $this;
    }

    /**
     * Make a GET request to the given URL.
     *
     * @param  string  $url
     * @param  array  $headers
     * @return $this
     */
    public function ping(string $url, array $headers = []): self
    {
        $this->httpClient->get($url, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Handle the running of a console command.
     *
     * @param string $commandToRun
     * @param bool $isInteractive
     */
    private function runCommand(string $commandToRun, bool $isInteractive = false): void
    {
        if ($isInteractive) {
            $this->runInteractiveCommand($commandToRun);

            return;
        }

        $process = new Process(explode(' ', $commandToRun));

        $process->setWorkingDirectory(base_path());

        $process->run(function ($type, $buffer) {
            if (app()->runningInConsole() && ! app()->runningUnitTests()) {
                echo $buffer;
            }
        });

        $output = $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();

        $this->setOutput($this->getOutput().$output);
    }

    /**
     * Handle the running of an interactive console command.
     *
     * @param string $commandToRun
     */
    private function runInteractiveCommand(string $commandToRun): void
    {
        passthru(escapeshellcmd($commandToRun), $status);
        if ($status == 0) {
            $this->setOutput($this->getOutput().' Interactive command completed');
        } else {
            $this->setOutput($this->getOutput().' Interactive command failed');
        }
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
    private function resetOutput()
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
    private function setOutput(string $output)
    {
        $this->output = $output;

        return $this;
    }
}
