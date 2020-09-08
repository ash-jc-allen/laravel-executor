<?php

namespace AshAllenDesign\LaravelExecutor\Tests\Unit\Classes;

use AshAllenDesign\LaravelExecutor\Classes\Executor;
use AshAllenDesign\LaravelExecutor\Exceptions\ExecutorException;
use AshAllenDesign\LaravelExecutor\Tests\Unit\TestCase;
use GuzzleHttp\Client;
use Hamcrest\Matchers;
use Illuminate\Support\Facades\App;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Mockery;

class ExecutorTest extends TestCase
{
    /** @test */
    public function closure_added_to_the_executor_can_be_run()
    {
        $executor = new class extends Executor {
            public function run(): Executor
            {
                return $this->runClosure(function () {
                    return 'I have just been run';
                });
            }
        };

        $executor->run();
        $this->assertEquals('I have just been run', $executor->getOutput());
    }

    /** @test */
    public function simple_desktop_notification_added_to_the_executor_can_be_run()
    {
        $notifierMock = Mockery::mock(NotifierFactory::class)->makePartial();

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->simpleDesktopNotification('Notification title', 'Notification body');
            }
        };

        $notification = (new Notification())->setTitle('Notification title')->setBody('Notification body')->setIcon($executor->logoPath);

        $notifierMock->expects('send')->once()->with(Matchers::equalTo($notification))->andReturnNull();

        $executor->run();
    }

    /** @test */
    public function completed_notification_can_be_displayed()
    {
        $notifierMock = Mockery::mock(NotifierFactory::class)->makePartial();

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->completeNotification();
            }
        };

        $notification = (new Notification())->setTitle('Executor complete!')
            ->setBody('The executor has been run successfully.')
            ->setIcon($executor->logoPath);

        $notifierMock->expects('send')->once()->with(Matchers::equalTo($notification))->andReturnNull();

        $executor->run();
    }

    /** @test */
    public function desktop_notification_added_to_the_executor_can_be_run()
    {
        $notifierMock = Mockery::mock(NotifierFactory::class);

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->desktopNotification(
                    (new Notification())->setTitle('Notification title')->setBody('Notification body')
                );
            }
        };

        $notification = (new Notification())->setTitle('Notification title')->setBody('Notification body');

        $notifierMock->expects('send')->once()->with(Matchers::equalTo($notification))->andReturnNull();

        $executor->run();
    }

    /** @test */
    public function external_command_added_to_the_executor_can_be_run()
    {
        $executor = new class extends Executor {
            public function run(): Executor
            {
                return $this->runExternal('echo ashallendesign');
            }
        };

        $executor->run();
        $this->assertEquals('ashallendesign', trim($executor->getOutput()));
    }

    /** @test */
    public function interactive_command_added_to_the_executor_can_be_run()
    {
        $executor = new class extends Executor {
            public function run(): Executor
            {
                return $this->runExternal('echo anything', true);
            }
        };

        $executor->run();
        $this->assertEquals('Interactive command completed', trim($executor->getOutput()));
    }

    /** @test */
    public function interactive_external_command_cannot_be_run_outside_of_console_mode()
    {
        $this->expectException(ExecutorException::class);
        $this->expectExceptionMessage('Interactive commands can only be run in the console.');

        App::shouldReceive('runningInConsole')->andReturnFalse();

        $executor = new class extends Executor {
            public function run(): Executor
            {
                return $this->runExternal('echo anything', true);
            }
        };

        $executor->run();
    }

    /** @test */
    public function interactive_artisan_command_cannot_be_run_outside_of_console_mode()
    {
        $this->expectException(ExecutorException::class);
        $this->expectExceptionMessage('Interactive commands can only be run in the console.');

        App::shouldReceive('runningInConsole')->andReturnFalse();

        $executor = new class extends Executor {
            public function run(): Executor
            {
                return $this->runArtisan('', true);
            }
        };

        $executor->run();
    }

    /** @test */
    public function url_can_be_pinged()
    {
        $guzzleMock = Mockery::mock(Client::class)->makePartial();
        $guzzleMock->expects('get')->withArgs(['localhost', ['headers' => []]])->once();

        $executor = new class(null, $guzzleMock) extends Executor {
            public function run(): Executor
            {
                return $this->ping('localhost');
            }
        };

        $executor->run();
        $this->assertEquals('', trim($executor->getOutput()));
    }

    /** @test */
    public function url_can_be_pinged_with_headers()
    {
        $guzzleMock = Mockery::mock(Client::class)->makePartial();
        $guzzleMock->expects('get')
            ->withArgs(['localhost', ['headers' => ['X-Request-Signature' => 'secret123']]])
            ->once();

        $executor = new class(null, $guzzleMock) extends Executor {
            public function run(): Executor
            {
                return $this->ping('localhost', ['X-Request-Signature' => 'secret123']);
            }
        };

        $executor->run();
        $this->assertEquals('', trim($executor->getOutput()));
    }
}
