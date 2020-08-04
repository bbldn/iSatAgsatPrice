<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AgsatCacheShowCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'agsat:cache:show';

    /** @var string $description */
    protected $description = 'Show cache';

    /**
     *
     */
    public function handle(): void
    {
        $this->output->text(Cache::get('JSONProducts', '[]'));
    }
}
