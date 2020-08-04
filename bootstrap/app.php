<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

$app = new Laravel\Lumen\Application(dirname(__DIR__));

$aliases = [
    Kozz\Laravel\Facades\Guzzle::class => 'Guzzle',
];


$app->withFacades(true, $aliases);
$app->withEloquent();

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Kozz\Laravel\Providers\Guzzle::class);


$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function (
    /** @noinspection PhpUnusedParameterInspection */
    $router
) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
