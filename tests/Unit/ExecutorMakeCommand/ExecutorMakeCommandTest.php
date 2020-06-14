<?php

namespace AshAllenDesign\LaravelExecutor\Tests\Unit\ExecutorMakeCommand;

use AshAllenDesign\LaravelExecutor\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ExecutorMakeCommandTest extends TestCase
{
    /** @test */
    public function executor_can_be_created_with_the_command()
    {
        $executorClass = app_path('Executor/PackageUnitTestExecutor.php');

        // Delete the executor and it's command if it exists
        // so that we can be sure we have a new slate for
        // testing.
        if (File::exists($executorClass)) {
            unlink($executorClass);
        }

        $this->assertFalse(File::exists($executorClass));

        Artisan::call('make:executor PackageUnitTestExecutor');

        $this->assertTrue(File::exists($executorClass));

        $this->assertEquals($this->expectedExecutorFileContents(), file_get_contents($executorClass));
    }

    private function expectedExecutorFileContents(): string
    {
        return "<?php

namespace App\Executor;

use AshAllenDesign\LaravelExecutor\Classes\Executor;

class PackageUnitTestExecutor extends Executor
{
    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     *
     * @return Executor
     */
    public function run(): Executor
    {
        return \$this->runArtisan('');
    }
}
";
    }
}
