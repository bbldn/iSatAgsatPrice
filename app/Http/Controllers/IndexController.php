<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function searchAction(Request $request): Response
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
     * @return Response
     */
    public function indexAction(): Response
    {
        $data = [
            'rate' => $this->getDollarRate(),
        ];

        return view('index', $data);
    }

    /**
     * @return Response
     */
    public function updateIndexAction(): Response
    {
        return view('update');
    }
}
