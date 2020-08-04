<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /** @var string[] $supportedFormats */
    protected $supportedFormats = [
        'json',
        'csv',
    ];

    /**
     * @param string $format
     * @return array
     */
    protected function validateFormat(string $format): ?array
    {
        if (false === in_array($format, $this->supportedFormats)) {
            return ['ok' => false, 'errors' => ['Unsupported format']];
        }

        return null;
    }

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
        $response = $this->validateFormat($format);
        if (null !== $response) {
            return response()->json($response);
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
        if (null !== $response) {
            return response()->json($response);
        }

        return response($data)->header('Content-Type', $contentType);
    }

    /**
     * @param Request $request
     * @param string $format
     * @return Response
     */
    public function productsSearchAction(Request $request, $format = 'json'): Response
    {
        $response = $this->validateFormat($format);
        if (null !== $response) {
            return response()->json($response);
        }

        if ('json' === $format) {
            $key = 'JSONProducts';
            $contentType = 'application/json';
        } else {
            $key = 'CSVProducts';
            $contentType = 'text/csv';
        }

        $data = Cache::get($key, null);
        $response = $this->validateData($data);
        if (null !== $response) {
            return response()->json($response);
        }

        $data = json_decode($data, true);
        //@TODO check false
        if (2 === (int)$request->get('currency', 1)) {
            $dollarRate = Cache::get('DollarRate');
            foreach ($data as &$item) {
                foreach ($item['prices'] as &$value) {
                    $value['price'] /= $dollarRate;
                }
            }
        }

        if (null !== $response) {
            return response()->json($response);
        }

        return response($data)->header('Content-Type', $contentType);
    }

    /**
     * @param string $format
     * @return Response
     */
    public function contactCategoriesSearchAction(string $format = 'json'): Response
    {
        $response = $this->validateFormat($format);
        if (null !== $response) {
            return response()->json($response);
        }

        if ('json' === $format) {
            $key = 'JSONContactCategories';
            $contentType = 'application/json';
        } else {
            $key = 'CSVContactCategories';
            $contentType = 'text/csv';
        }

        $data = Cache::get($key);

        $response = $this->validateData($data);
        if (null !== $response) {
            return response()->json($response);
        }

        return response($data)->header('Content-Type', $contentType);
    }

    /**
     * @return Response
     */
    public function getDollarRateAction(): Response
    {
        return response()->json(['rate' => Cache::get('DollarRate', 40)]);
    }
}
