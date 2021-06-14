<?php

namespace App\Console;

use App\Console\Commands\AgsatCacheShowCommand;
use App\Console\Commands\AgsatCacheUpdateCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var string[]
     *
     * @psalm-var list<class-string>
     */
    protected $commands = [
        AgsatCacheShowCommand::class,
        AgsatCacheUpdateCommand::class,
    ];
}