<?php

namespace App\Http\Controllers;

use App\Console\Commands\AgsatCacheUpdateCommand;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class UpdateController extends Controller
{
    /**
     * @return Response
     */
    public function updateAction(): Response
    {
        Artisan::call(AgsatCacheUpdateCommand::class);

        $data = [
            'ok' => true,
            'data' => [
                'status' => trim(Artisan::output()),
            ],
        ];

        return response()->json($data);
    }
}
