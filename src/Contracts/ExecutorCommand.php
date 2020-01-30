<?php

namespace AshAllenDesign\LaravelExecutor\Contracts;

interface ExecutorCommand
{
    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     */
    public function run(): void;
}