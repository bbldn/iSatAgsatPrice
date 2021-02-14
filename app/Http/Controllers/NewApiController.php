<?php

namespace App\Http\Controllers;

use App\Helpers\CacheEnum;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class NewApiController extends Controller
{
    /**
     * @return Response
     */
    public function categoriesAction(): Response
    {
        $data = Cache::get(CacheEnum::CategoriesV2);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function productsAction(): Response
    {
        $data = Cache::get(CacheEnum::ProductsV2);
        $data = json_decode($data, true);

        return response($data)->header('Content-Type', 'application/json');
    }

    /**
     * @return Response
     */
    public function customerGroupsAction(): Response
    {
        $data = Cache::get(CacheEnum::CustomerGroupsV2);

        return response($data)->header('Content-Type', 'application/json');
    }
}
