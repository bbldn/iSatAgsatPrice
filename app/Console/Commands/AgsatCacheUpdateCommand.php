<?php

namespace App\Console\Commands;

use App\Helpers\CacheEnum;
use App\Services\AgsatService;
use App\Services\NewApiService;
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
     * @param NewApiService $newApiService
     */
    public function handle(
        AgsatService $agsat,
        NewApiService $newApiService
    ): void
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
        Cache::forever(CacheEnum::All, json_encode($data));
        Cache::forever(CacheEnum::Categories, json_encode($data['categories']));
        Cache::forever(CacheEnum::ContactCategories, json_encode($data['contact_categories']));

        $products = $this->convertToDollar($data['products'], $rate);
        Cache::forever(CacheEnum::Products, json_encode($products));

        $products = $this->convertToGRN($data['products'], $rate);
        Cache::forever(CacheEnum::ProductsGRN, json_encode($products));

        /** For New Api */
        $data = $newApiService->convertToNewApiFormat($data, $rate);
        Cache::forever(CacheEnum::AllV2, json_encode($data));
        Cache::forever(CacheEnum::ProductsV2, json_encode($data['products']));
        Cache::forever(CacheEnum::CategoriesV2, json_encode($data['categories']));
        Cache::forever(CacheEnum::CustomerGroupsV2, json_encode($data['customerGroups']));
        /** End For New Api */

        $this->info('ok');
    }
}
