<?php

namespace App\Http\Controllers;

use App\Other\Agsat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    protected function getProducts(Agsat $agsat)
    {
        $json = Cache::get('CACH1E');

        if ($json == null) {
            $json = $agsat->getProducts();
            Cache::forever('CACH1E', $json);
        }

        return json_decode(json_decode($json, true), true);
    }

    protected function getDollarRate(Agsat $agsat)
    {
        $rate = Cache::get('DOLLARRATE');

        if ($rate == null) {
            $rate = $agsat->getDollarRate();
            Cache::forever('DOLLARRATE', $rate);
        }

        return $rate;
    }

    public function searchAction(Request $request, Agsat $agsat)
    {
        $data = $this->getProducts($agsat);

        $products = collect($data['data']['products']);
        $query = $request->get('q');

        if (isset($query)) {
            $products = $products->filter(function ($item) use ($query) {
                return false !== stristr($item['name'], $query);
            });
        }

        $data = [
            'products' => $products,
            'rate' => $this->getDollarRate($agsat),
        ];

        return view('search', $data);
    }

    public function indexAction(Agsat $agsat)
    {
        $rate = $this->getDollarRate($agsat);
        return view('index', ['rate' => $rate]);
    }
}
