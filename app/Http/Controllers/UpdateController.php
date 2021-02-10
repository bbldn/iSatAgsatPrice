<?php

namespace App\Http\Controllers;

use App\Console\Commands\AgsatCacheUpdateCommand;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Routing\Controller;

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
                'status' => Artisan::call(AgsatCacheUpdateCommand::class),
            ],
        ];

        return response()->json($data);
    }
}
