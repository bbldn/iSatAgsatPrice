<?php

namespace App\Console\Commands;

use App\Other\Agsat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use League\Csv\Writer;

class UpdateCache extends Command
{
    protected $signature = 'agsat:update';
    protected $description = 'Update cache';

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

    public function handle(Agsat $agsat)
    {
        $rate = $agsat->getDollarRate();
        Cache::forever('DollarRate', $rate);

        $arr = json_decode(json_decode($agsat->getAll(), true), true);

        if ($arr['status'] != 'ok') {
            $this->info('error');
            return;
        }

        $data = $arr['data'];

        foreach ($data['products'] as &$product) {
            foreach ($product['prices'] as &$price) {
                if ($price['category_id'] != 1) {
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

    protected function productsToCSV(array $data, $header = null)
    {
        $productsCSV = [];
        foreach ($data as $product) {
            $productCSV = [];
            foreach ($product as $item) {
                if (is_array($item)) {
                    foreach ($item as $it) {
                        $productCSV[] = $it['category_id'] . ' - ' . $it['price'];
                    }
                    continue;
                }
                if (is_bool($item)) {
                    $item = intval($item);
                }
                $productCSV[] = $item;
            }
            $productsCSV[] = $productCSV;
        }

        return $this->toCSV($productsCSV, $header);
    }

    protected function categoriesToCSV(array $data, $header = null)
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

    protected function contactCategoriesToCSV(array $data, $header = null)
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

    protected function toCSV(array $data, $header = null)
    {
        $csv = Writer::createFromString('');

        if ($header != null) {
            $csv->insertOne($header);
        }

        $csv->insertAll($data);

        return $csv->getContent();
    }
}
