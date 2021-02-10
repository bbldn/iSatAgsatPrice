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
     * @param array $products
     * @param float $rate
     * @return array
     */
    private function convertToDollar(array $products, float $rate): array
    {
        $data = [];
        foreach ($products as $product) {
            $prices = [];
            foreach ($product['prices'] as $price) {
                if (1 === $price['category_id']) {
                    $price['price'] = round($price['price'] / $rate, 2);
                }

                $prices[] = $price;
            }
            $product['prices'] = $prices;
            $data[] = $product;
        }

        return $data;
    }

    /**
     * @param array $products
     * @param float $rate
     * @return array
     */
    private function convertToGRN(array $products, float $rate): array
    {
        $data = [];
        foreach ($products as $product) {
            $prices = [];
            foreach ($product['prices'] as $price) {
                if ($price['category_id'] > 1) {
                    $price['price'] *= $rate;
                }

                $prices[] = $price;
            }
            $product['prices'] = $prices;
            $data[] = $product;
        }

        return $data;
    }

    /**
     * @param AgsatService $agsat
     */
    public function handle(AgsatService $agsat): void
    {
        $rate = (float)$agsat->getGRNRate();
        Cache::forever(CacheEnum::GRNRate, $rate);

        $data = json_decode($agsat->getAll(), true);
        $data = json_decode($data, true);

        if ('ok' !== $data['status']) {
            $this->info('error');

            return;
        }

        $data = $data['data'];
        Cache::forever(CacheEnum::JSONAll, json_encode($data));
        Cache::forever(CacheEnum::JSONCategories, json_encode($data['categories']));
        Cache::forever(CacheEnum::JSONContactCategories, json_encode($data['contact_categories']));

        $products = $this->convertToDollar($data['products'], $rate);
        Cache::forever(CacheEnum::JSONProducts, json_encode($products));

        $products = $this->convertToGRN($data['products'], $rate);
        Cache::forever(CacheEnum::JSONProductsGRN, json_encode($products));

        $this->info('ok');
    }
}
