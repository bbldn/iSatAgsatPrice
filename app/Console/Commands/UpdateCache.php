<?php

namespace App\Console\Commands;

use App\Other\Agsat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateCache extends Command
{
    protected $signature = 'agsat:update';
    protected $description = 'Update cache';

    public function handle(Agsat $agsat)
    {
        $rate = $agsat->getDollarRate();
        Cache::forever('DOLLARRATE', $rate);

        $json = $agsat->getProducts();
        Cache::forever('CACH1E', $json);
        $this->info('ok');
    }
}
