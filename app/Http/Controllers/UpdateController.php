<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class UpdateController extends Controller
{
    /**
     * @return Response
     */
    public function updateAction(): Response
    {
        $data = [
            'ok' => true,
            'data' => [
                'status' => Artisan::call('agsat:update'),
            ],
        ];

        return response()->json($data);
    }
}
