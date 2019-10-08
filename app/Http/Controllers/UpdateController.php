<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class UpdateController extends Controller
{
    public function updateAction()
    {
        Artisan::call('agsat:update');
        return response()->json(['ok' => true]);
    }
}
