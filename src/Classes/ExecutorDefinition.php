<?php

namespace AshAllenDesign\LaravelExecutor\Classes;

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
     * Run the commands defined that are in the
     * executor definition.
     */
    public function run(): string
    {
        $this->executor->resetOutput();

        $this->definition();

        return $this->executor->getOutput();
    }

    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     *
     * @return void
     */
    abstract public function definition(): void;
}
