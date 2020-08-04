<?php

namespace App\Console;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var string[] $commands
     */
    protected $commands = [
        \App\Console\Commands\AgsatCacheUpdateCommand::class,
        \App\Console\Commands\AgsatCacheShowCommand::class,
        \Mlntn\Console\Commands\Serve::class,
    ];
}
