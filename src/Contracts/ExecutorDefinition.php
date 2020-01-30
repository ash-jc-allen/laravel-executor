<?php

namespace AshAllenDesign\LaravelExecutor\Contracts;

interface ExecutorDefinition
{
    /**
     * Define the commands here that are to be run when
     * this executor class is called.
     */
    public function run(): void;
}