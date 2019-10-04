<?php

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->get('/', 'IndexController@indexAction');
$router->get('search', 'IndexController@searchAction');
$router->get('update', 'UpdateController@indexAction');
$router->get('/api/update', 'UpdateController@updateAction');

