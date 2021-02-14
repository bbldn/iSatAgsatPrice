<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
