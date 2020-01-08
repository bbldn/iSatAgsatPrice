<?php

namespace App\Http\Controllers;

use App\Other\Agsat;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    protected function getProducts()
    {
        return json_decode(Cache::get('JSONProducts'), true);
    }

    protected function getDollarRate()
    {
        return Cache::get('DollarRate');
    }

    public function searchAction(Request $request)
    {
        $products = Collection::make($this->getProducts());

        if ($request->has('q')) {
            $query = $request->get('q');
            $products = $products->filter(function ($item) use ($query) {
                return false !== mb_stristr($item['name'], $query) || false !== mb_stristr($item['sku'], $query);
            });
        }

        $data = [
            'products' => $products,
            'rate' => $this->getDollarRate(),
        ];

        return view('search', $data);
    }

    public function indexAction(Agsat $agsat)
    {
        $rate = $this->getDollarRate($agsat);
        return view('index', ['rate' => $rate]);
    }

    public function updateIndexAction()
    {
        return view('update');
    }
}
