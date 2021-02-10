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
     * @param array|null $data
     * @return array|null
     */
    protected function validateData(?array $data): ?array
    {
        if (null === $data) {
            return [
                'ok' => false,
                'errors' => [
                    'No data found, refresh cache',
                ],
            ];
        }

        return null;
    }

    /**
     * @return Response
     */
    public function categoriesSearchAction(): Response
    {
        $data = Cache::get(CacheEnum::JSONCategories);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function productsSearchAction(Request $request): Response
    {
        /**
         * GRN - 1
         * Dollar - 2
         */
        $currencyId = (int)$request->get('currency', 1);
        $key = 1 === $currencyId ? CacheEnum::JSONProductsGRN : CacheEnum::JSONProducts;

        $data = Cache::get($key, null);
        $data = json_decode($data, true);

        if (false === $data) {
            return response()->json($this->validateData($data));
        }

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function contactCategoriesSearchAction(): Response
    {
        $data = Cache::get(CacheEnum::JSONContactCategories);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function getGRNRateAction(): Response
    {
        return response()->json(['rate' => Cache::get(CacheEnum::GRNRate, 40)]);
    }
}
