<?php

namespace App\Http\Controllers;

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
        $products = json_decode(Cache::get('JSONProducts'), true);
        if (false === $products) {
            return [];
        }

        return $products;
    }

    /**
     * @return float|null
     */
    protected function getDollarRate(): ?float
    {
        return Cache::get('DollarRate');
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
            'rate' => $this->getDollarRate(),
        ];

        return view('search', $data);
    }

    /**
     * @return View
     */
    public function indexAction(): View
    {
        $data = [
            'rate' => $this->getDollarRate(),
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
