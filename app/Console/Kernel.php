<?php

namespace App\Console;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var string[] $commands
     */
    protected $commands = [
        \App\Console\Commands\UpdateCache::class,
        \Mlntn\Console\Commands\Serve::class,
    ];
}
