<?php

namespace App\Console\Commands;

use App\Services\AgsatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

class AgsatCacheUpdateCommand extends Command
{
    /** @var string */
    protected $description = 'Update cache';

    /** @var string */
    protected $signature = 'agsat:cache:update';

    /** @var array */
    private $headers = [
        'products' => [
            'id', 'name', 'url',
            'sku', 'sku_id', 'category_id',
            'price_in_usd', 'category_id - roznica',
            'category_id - diler', 'category_id - opt', 'category_id - partner',
        ],
        'categories' => [
            'id', 'name', 'parent_id', 'url',
        ],
        'contact_categories' => [
            'id', 'name',
        ],
    ];

    /**
     * @param AgsatService $agsat
     * @throws CannotInsertRecord
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
        Cache::forever('CSVProducts', $this->productsToCSV($arr['products'], $this->headers['products']));
        Cache::forever('CSVCategories', $this->categoriesToCSV($arr['categories'], $this->headers['categories']));
        Cache::forever(
            'CSVContactCategories',
            $this->contactCategoriesToCSV($arr['contact_categories'], $this->headers['contact_categories'])
        );

        $this->info('ok');
    }

    /**
     * @param array $data
     * @param array|null $header
     * @return string
     * @throws CannotInsertRecord
     */
    private function productsToCSV(array $data, ?array $header = null): string
    {
        $productsCSV = [];
        foreach ($data as $product) {
            $productCSV = [];
            foreach ($product as $item) {
                if (true === is_array($item)) {
                    foreach ($item as $it) {
                        $productCSV[] = "{$it['category_id']} - {$it['price']}";
                    }
                    continue;
                }

                if (true === is_bool($item)) {
                    $item = intval($item);
                }

                $productCSV[] = $item;
            }
            $productsCSV[] = $productCSV;
        }

        return $this->toCSV($productsCSV, $header);
    }

    /**
     * @param array $data
     * @param array|null $header
     * @return string
     * @throws CannotInsertRecord
     */
    private function categoriesToCSV(array $data, ?array $header = null): string
    {
        $categoriesCSV = [];
        foreach ($data as $category) {
            $categoryCSV = [];
            foreach ($category as $item) {
                $categoryCSV[] = $item;
            }
            $categoriesCSV[] = $categoryCSV;
        }

        return $this->toCSV($categoriesCSV, $header);
    }

    /**
     * @param array $data
     * @param array|null $header
     * @return string
     * @throws CannotInsertRecord
     */
    private function contactCategoriesToCSV(array $data, ?array $header = null): string
    {
        $contactCategoriesCSV = [];
        foreach ($data as $contactCategory) {
            $contactCategoryCSV = [];
            foreach ($contactCategory as $item) {
                $contactCategoryCSV[] = $item;
            }
            $contactCategoriesCSV[] = $contactCategoryCSV;
        }

        return $this->toCSV($contactCategoriesCSV, $header);
    }

    /**
     * @param array $data
     * @param array|null $header
     * @return string
     * @throws CannotInsertRecord
     */
    private function toCSV(array $data, ?array $header = null): string
    {
        $csv = Writer::createFromString();

        if (true === is_array($header)) {
            $csv->insertOne($header);
        }

        $csv->insertAll($data);

        return $csv->getContent();
    }
}
