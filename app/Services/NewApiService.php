<?php

namespace App\Services;

class NewApiService
{
    /**
     * @param array $customerGroups
     * @return array
     */
    private function convertCustomerGroup(array $customerGroups): array
    {
        $data = [
            'id' => 1,
            'name' => 'Розница',
        ];

        foreach ($customerGroups as $customerGroup) {
            $data[] = [
                'name' => $customerGroup['name'],
                'id' => (int)$customerGroup['id'],
            ];
        }

        return $data;
    }

    /**
     * @param array $categories
     * @return array
     */
    private function convertCategories(array $categories): array
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'name' => $category['name'],
                'id' => (int)$category['id'],
                'url' => $category['frontend_url'],
                'parent_id' => (int)$category['parent_id'],
            ];
        }

        return $data;
    }

    private function convertProducts(array $products, float $rate): array
    {
        $result = [];
        foreach ($products as $product) {
            $prices = [];
            foreach ($product['prices'] as $price) {
                if (1 === $price['category_id']) {
                    $price['price'] = round($price['price'] / $rate, 2);
                }

                $prices[] = [
                    'price' => (float)$price['price'],
                    'customer_group_id' => (int)$price['category_id'],
                ];
            }

            $result[] = [
                'prices' => $prices,
                'name' => $product['name'],
                'id' => (int)$product['id'],
                'sku' => (int)$product['sku'],
                'url' => $product['frontend_url'],
                'sku_id' => (int)$product['sku_id'],
                'category_id' => $product['category_id'],
            ];
        }

        return $result;
    }

    /**
     * @param array $data
     * @param float $rate
     * @return array
     */
    public function convertToNewApiFormat(array $data, float $rate): array
    {
        $categories = $this->convertCategories($data['categories']);
        $products = $this->convertProducts($data['products'], $rate);
        $customerGroups = $this->convertCustomerGroup($data['contact_categories']);

        return [
            'rate' => $rate,
            'products' => $products,
            'categories' => $categories,
            'customerGroups' => $customerGroups,
        ];
    }
}
