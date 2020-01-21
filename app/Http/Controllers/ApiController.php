<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    protected $supportedFormats = ['json', 'csv'];

    protected function validateFormat($format)
    {
        if (!in_array($format, $this->supportedFormats)) {
            return response()->json(['ok' => false, 'errors' => ['Unsupported format']]);
        }

        return null;
    }

    protected function validateData($data)
    {
        if ($data == null) {
            return response()->json(['ok' => false, 'errors' => ['No data found, refresh cache']]);
        }

        return null;
    }

    public function categoriesSearchAction($format = 'json')
    {
        $response = $this->validateFormat($format);
        if ($response != null) {
            return $response;
        }

        if ($format == 'json') {
            $key = 'JSONCategories';
            $contentType = 'application/json';
        } else {
            $key = 'CSVCategories';
            $contentType = 'text/csv';
        }

        $data = Cache::get($key);

        $response = $this->validateData($data);
        if ($response != null) {
            return $response;
        }

        return response($data)->header('Content-Type', $contentType);
    }

    public function productsSearchAction(Request $request, $format = 'json')
    {
        $response = $this->validateFormat($format);
        if ($response != null) {
            return $response;
        }

        if ($format == 'json') {
            $key = 'JSONProducts';
            $contentType = 'application/json';
        } else {
            $key = 'CSVProducts';
            $contentType = 'text/csv';
        }

        $data = Cache::get($key);

        $response = $this->validateData($data);

        $data = json_decode($data, true);


        if ($request->get('currency', 1) == 2) {
            $dollarRate = Cache::get('DollarRate');
            foreach ($data as &$item) {
                foreach ($item['prices'] as &$value) {
                    $value['price'] /= $dollarRate;
                }
            }
        }

        if ($response != null) {
            return $response;
        }

        return response($data)->header('Content-Type', $contentType);
    }

    public function contactCategoriesSearchAction($format = 'json')
    {
        $response = $this->validateFormat($format);
        if ($response != null) {
            return $response;
        }

        if ($format == 'json') {
            $key = 'JSONContactCategories';
            $contentType = 'application/json';
        } else {
            $key = 'CSVContactCategories';
            $contentType = 'text/csv';
        }

        $data = Cache::get($key);

        $response = $this->validateData($data);
        if ($response != null) {
            return $response;
        }

        return response($data)->header('Content-Type', $contentType);
    }

    public function getDollarRateAction()
    {
        return response()->json(['rate' => Cache::get('DollarRate', 40)]);
    }
}
