<?php

require_once __DIR__ . '/../vendor/autoload.php';

$path = dirname(__DIR__);
(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables($path))->bootstrap();

$app = new Laravel\Lumen\Application($path);

$app->withFacades();
//$app->withEloquent();

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

$app->register(App\Providers\AppServiceProvider::class);

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function (
    /** @noinspection PhpUnusedParameterInspection */
    $router
) {
    require __DIR__ . '/../routes/web.php';
});

return $app;