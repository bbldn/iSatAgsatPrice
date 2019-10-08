<?php

namespace App\Http\Controllers;

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

    public function productsSearchAction($format = 'json')
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
}
