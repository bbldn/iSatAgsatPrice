<?php

namespace App\Console\Commands;

use App\Services\AgsatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AgsatCacheUpdateCommand extends Command
{
    /** @var string */
    protected $description = 'Update cache';

    /** @var string */
    protected $signature = 'agsat:cache:update';

    /**
     * @param AgsatService $agsat
     */
    public function handle(AgsatService $agsat): void
    {
        $rate = $agsat->getHryvniaRate();
        Cache::forever('DollarRate', $rate);

        $arr = json_decode(json_decode($agsat->getAll(), true), true);

        if ('ok' !== $arr['status']) {
            $this->info('error');

            return;
        }

        $arr = $arr['data'];
        foreach ($arr['products'] as &$product) {
            foreach ($product['prices'] as &$price) {
                if (1 !== $price['category_id']) {
                    $price['price'] *= $rate;
                }
            }
        }

        Cache::forever('JSONAll', json_encode($arr));
        Cache::forever('JSONProducts', json_encode($arr['products']));
        Cache::forever('JSONCategories', json_encode($arr['categories']));
        Cache::forever('JSONContactCategories', json_encode($arr['contact_categories']));

        $this->info('ok');
    }
}
