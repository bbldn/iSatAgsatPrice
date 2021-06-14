<?php

namespace App\Console;

use App\Console\Commands\AgsatCacheShowCommand;
use App\Console\Commands\AgsatCacheUpdateCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Mlntn\Console\Commands\Serve;

class Kernel extends ConsoleKernel
{
    /**
     * @var string[]
     *
     * @psalm-var list<class-string>
     */
    protected $commands = [
        Serve::class,
        AgsatCacheShowCommand::class,
        AgsatCacheUpdateCommand::class,
    ];
}