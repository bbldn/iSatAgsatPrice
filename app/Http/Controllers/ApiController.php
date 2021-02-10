<?php

namespace App\Http\Controllers;

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
            return ['ok' => false, 'errors' => ['No data found, refresh cache']];
        }

        return null;
    }

    /**
     * @param string $format
     * @return Response
     */
    public function categoriesSearchAction(string $format = 'json'): Response
    {
        $data = Cache::get('JSONCategories');

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function productsSearchAction(Request $request): Response
    {
        $data = Cache::get('JSONProducts', null);
        $data = json_decode($data, true);

        if (false === $data) {
            return response()->json($this->validateData(null));
        }

        if (2 === (int)$request->get('currency', 1)) {
            $dollarRate = Cache::get('DollarRate');
            foreach ($data as &$item) {
                foreach ($item['prices'] as &$value) {
                    $value['price'] /= $dollarRate;
                }
            }
        }

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function contactCategoriesSearchAction(): Response
    {
        $data = Cache::get('JSONContactCategories');

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function getDollarRateAction(): Response
    {
        return response()->json(['rate' => Cache::get('DollarRate', 40)]);
    }
}
