<?php

namespace App\Console\Commands;

use App\Helpers\CacheEnum;
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
        $rate = $agsat->getGRNRate();
        Cache::forever(CacheEnum::GRNRate, $rate);

        $data = json_decode($agsat->getAll(), true);
        $data = json_decode($data, true);

        if ('ok' !== $data['status']) {
            $this->info('error');

            return;
        }

        $data = $data['data'];
        Cache::forever(CacheEnum::JSONAll, json_encode($data));
        Cache::forever(CacheEnum::JSONProducts, json_encode($data['products']));
        Cache::forever(CacheEnum::JSONCategories, json_encode($data['categories']));
        Cache::forever(CacheEnum::JSONContactCategories, json_encode($data['contact_categories']));

        foreach ($data['products'] as &$product) {
            foreach ($product['prices'] as &$price) {
                if (1 !== $price['category_id']) {
                    $price['price'] *= $rate;
                }
            }
        }


        Cache::forever(CacheEnum::JSONProductsGRN, json_encode($data['products']));

        $this->info('ok');
    }
}
