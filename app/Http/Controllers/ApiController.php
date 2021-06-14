<?php

namespace App\Http\Controllers;

use App\Helpers\CacheEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{

    /**
     * @return Response
     */
    public function categoriesAction(): Response
    {
        $data = Cache::get(CacheEnum::Categories);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function productsAction(Request $request): Response
    {
        /**
         * GRN - 1
         * Dollar - 2
         */
        $currencyId = (int)$request->get('currency', 1);
        $key = 1 === $currencyId ? CacheEnum::ProductsGRN : CacheEnum::Products;

        $data = Cache::get($key, null);
        $data = json_decode($data, true);

        if (false === $data) {
            return response()->json([]);
        }

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function contactCategoriesAction(): Response
    {
        $data = Cache::get(CacheEnum::ContactCategories);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function rateAction(): Response
    {
        $data = json_encode(['rate' => Cache::get(CacheEnum::GRNRate, 40)]);

        return response($data)->header('Content-Type', 'application/json');
    }
}