<?php

namespace App\Http\Controllers;

use App\Helpers\CacheEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Laravel\Lumen\Routing\Controller;

class IndexController extends Controller
{
    /**
     * @param int|null $currencyId
     * @return array
     */
    protected function getProducts(?int $currencyId = 1): array
    {
        $key = 2 === $currencyId ? CacheEnum::Products : CacheEnum::ProductsGRN;
        $products = json_decode(Cache::get($key), true);
        if (false === $products) {
            return [];
        }

        return $products;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function searchAction(Request $request): View
    {
        $currencyId = $request->get('currency_id', 1);
        $products = $this->getProducts($currencyId);
        if (true === $request->has('q')) {
            $query = $request->get('q');

            $products = array_filter($products, function ($item) use ($query) {
                return false !== mb_stristr($item['name'], $query) || false !== mb_stristr($item['sku'], $query);
            });
        }

        $data = [
            'products' => $products,
            'rate' => Cache::get(CacheEnum::GRNRate),
        ];

        return view('search', $data);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request): View
    {
        $currencyId = (int)$request->get('currency_id', 1);
        $data = [
            'currencyId' => $currencyId,
            'rate' => Cache::get(CacheEnum::GRNRate),
        ];

        return view('index', $data);
    }


    /**
     * @return View
     */
    public function updateIndexAction(): View
    {
        return view('update');
    }
}
