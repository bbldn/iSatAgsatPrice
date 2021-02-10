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
     * @return array
     */
    protected function getProducts(): array
    {
        $products = json_decode(Cache::get(CacheEnum::JSONProductsGRN), true);
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
        $products = $this->getProducts();
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
     * @return View
     */
    public function indexAction(): View
    {
        $data = [
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
