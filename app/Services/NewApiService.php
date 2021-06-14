<?php

namespace App\Services;

/**
 * @psalm-type CustomerGroupPsalm = array{id: int, name: string}
 * @psalm-type CategoryPsalm = array{id: int, url: string, name: string, parent_id: int}
 * @psalm-type ProductPricePsalm = array{price: float, currency_id: int, customer_group_id: int}
 * @psalm-type ProductPsalm = array{id: int, shu_id: int, category_id: int, url: string, sku: string, name: string, prices: list<ProductPricePsalm>}
 * @psalm-type NewApiFormatPsalm = array{
 *     rate: float,
 *     products: list<ProductPsalm>,
 *     categories: list<CategoryPsalm>,
 *     customerGroups: list<CustomerGroupPsalm>
 * }
 */
class NewApiService
{
    /**
     * @param array $customerGroups
     * @return array
     *
     * @psalm-return list<CustomerGroupPsalm>
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
     * @return array
     *
     * @psalm-return list<CategoryPsalm>
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
     * @return array
     *
     * @psalm-return list<ProductPsalm>
     */
    private function convertProducts(array $products): array
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
     * @return array
     *
     * @psalm-return NewApiFormatPsalm
     */
    public function convertToNewApiFormat(array $data, float $rate): array
    {
        $products = $this->convertProducts($data['products']);
        $categories = $this->convertCategories($data['categories']);
        $customerGroups = $this->convertCustomerGroup($data['contact_categories']);

        return [
            'rate' => $rate,
            'products' => $products,
            'categories' => $categories,
            'customerGroups' => $customerGroups,
        ];
    }
}