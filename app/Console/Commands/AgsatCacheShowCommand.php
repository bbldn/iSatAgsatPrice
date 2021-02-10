<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AgsatCacheShowCommand extends Command
{
    /** @var string */
    protected $description = 'Show cache';

    /** @var string */
    protected $signature = 'agsat:cache:show';

    /**
     *
     */
    public function handle(): void
    {
        $this->output->text(Cache::get('JSONProducts', '[]'));
    }
}
