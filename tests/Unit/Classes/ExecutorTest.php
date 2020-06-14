<?php

namespace AshAllenDesign\LaravelExecutor\Tests\Unit\Classes;

use AshAllenDesign\LaravelExecutor\Classes\Executor;
use AshAllenDesign\LaravelExecutor\Tests\Unit\TestCase;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

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
        // TODO: Improve this test!

        $notifierMock = \Mockery::mock(NotifierFactory::class)->makePartial();
        $notifierMock->expects('send')->once()->withAnyArgs()->andReturnNull();

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->simpleDesktopNotification('Notification title', 'Notification body');
            }
        };

        $executor->run();
    }

    /** @test */
    public function completed_notification_can_be_displayed()
    {
        // TODO: Improve this test!

        $notifierMock = \Mockery::mock(NotifierFactory::class)->makePartial();
        $notifierMock->expects('send')->once()->withAnyArgs()->andReturnNull();

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->completeNotification();
            }
        };

        $executor->run();
    }

    /** @test */
    public function desktop_notification_added_to_the_executor_can_be_run()
    {
        // TODO: Improve this test!

        $notifierMock = \Mockery::mock(NotifierFactory::class);
        $notifierMock->expects('send')->once()->withAnyArgs()->andReturnNull();

        $executor = new class($notifierMock) extends Executor {
            public function run(): Executor
            {
                return $this->desktopNotification(
                    (new Notification())->setTitle('Notification title')->setBody('Notification body')
                );
            }
        };

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
}