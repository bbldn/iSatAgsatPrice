<?php

namespace App\Console\Commands;

use App\Services\Agsat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

class UpdateCache extends Command
{
    /** @var string $signature */
    protected $signature = 'agsat:update';

    /** @var string $description */
    protected $description = 'Update cache';

    /** @var array $headers */
    protected $headers = [
        'products' => [
            'id', 'name', 'url',
            'sku', 'sku_id', 'category_id',
            'price_in_usd', 'category_id - roznica',
            'category_id - diler', 'category_id - opt', 'category_id - partner'
        ],
        'categories' => [
            'id', 'name', 'parent_id', 'url'
        ],
        'contact_categories' => [
            'id', 'name'
        ]
    ];

    /**
     * @param Agsat $agsat
     * @throws CannotInsertRecord
     */
    public function handle(Agsat $agsat): void
    {
        $rate = $agsat->getDollarRate();
        Cache::forever('DollarRate', $rate);

        $arr = json_decode(json_decode($agsat->getAll(), true), true);

        if ('ok' !== $arr['status']) {
            $this->info('error');

            return;
        }

        $data = $arr['data'];

        foreach ($data['products'] as &$product) {
            foreach ($product['prices'] as &$price) {
                if (1 !== $price['category_id']) {
                    $price['price'] *= $rate;
                }
            }
        }

        Cache::forever('JSONAll', json_encode($data));

        Cache::forever('JSONProducts', json_encode($data['products']));
        Cache::forever('JSONCategories', json_encode($data['categories']));
        Cache::forever('JSONContactCategories', json_encode($data['contact_categories']));

        Cache::forever('CSVProducts', $this->productsToCSV($data['products'], $this->headers['products']));
        Cache::forever('CSVCategories', $this->categoriesToCSV($data['categories'], $this->headers['categories']));
        Cache::forever('CSVContactCategories', $this->contactCategoriesToCSV($data['contact_categories'], $this->headers['contact_categories']));

        $this->info('ok');
    }

    /**
     * @param array $data
     * @param array|null $header
     * @return string
     * @throws CannotInsertRecord
     */
    protected function productsToCSV(array $data, ?array $header = null): string
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
    protected function categoriesToCSV(array $data, ?array $header = null): string
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
    protected function contactCategoriesToCSV(array $data, ?array $header = null): string
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
    protected function toCSV(array $data, ?array $header = null): string
    {
        $csv = Writer::createFromString();

        if (true === is_array($header)) {
            $csv->insertOne($header);
        }

        $csv->insertAll($data);

        return $csv->getContent();
    }
}
