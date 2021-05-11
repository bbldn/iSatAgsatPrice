<?php

namespace App\Providers;

use App\Contexts\AgsatContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AgsatContext::class, function () {
            $login = env('ALOGIN');
            $password = env('APASSWORD');
            $userAgent = <<<TEXT
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36
TEXT;

            return new AgsatContext($login, $password, $userAgent);
        });
    }
}
