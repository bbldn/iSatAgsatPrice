<?php

namespace App\Services;

class NewApiService
{
    /**
     * @param array $customerGroups
     * @return array<array{
     *     id: int,
     *     name: string
     * }>
     */
    private function convertCustomerGroup(array $customerGroups): array
    {
        $data = [
            ['id' => 1, 'name' => 'Розница'],
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
     * @return array<array{
     *     id: int,
     *     url: string,
     *     name: string,
     *     parent_id: int,
     * }>
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

    /**
     * @param array $products
     * @param float $rate
     * @return array<array{
     *     id: int,
     *     sku: int,
     *     url: string,
     *     shu_id: int,
     *     name: string,
     *     category_id: int,
     *     prices: array<array{
     *         price: int,
     *         customer_group_id: int,
     *     }>
     * }>
     */
    private function convertProducts(array $products, float $rate): array
    {
        /**
         * CurrencyId:
         *     Dollar - 1
         *     GRN - 2
         */
        $result = [];
        foreach ($products as $product) {
            $prices = [];
            foreach ($product['prices'] as $price) {
                $categoryId = (int)$price['category_id'];

                $prices[] = [
                    'price' => (float)$price['price'],
                    'customer_group_id' => $categoryId,
                    'currency_id' => 1 === $categoryId ? 2 : 1,
                ];
            }

            $result[] = [
                'prices' => $prices,
                'name' => $product['name'],
                'id' => (int)$product['id'],
                'sku' => (int)$product['sku'],
                'url' => $product['frontend_url'],
                'sku_id' => (int)$product['sku_id'],
                'category_id' => (int)$product['category_id'],
            ];
        }

        return $result;
    }

    /**
     * @param array $data
     * @param float $rate
     * @return array{
     *     rate: float,
     *     categories: array<array{
     *         id: int,
     *         url: string,
     *         name: string,
     *         parent_id: int,
     *     }>,
     *     products: array<array{
     *         id: int,
     *         sku: int,
     *         url: string,
     *         shu_id: int,
     *         name: string,
     *         category_id: int,
     *         prices: array<array{
     *             price: int,
     *             customer_group_id: int,
     *         }>
     *     }>,
     *     customerGroups: array<array{
     *         id: int,
     *         name: string
     *     }>,
     * }
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