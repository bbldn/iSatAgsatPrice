<?php

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->get('/', 'IndexController@indexAction');
$router->get('search', 'IndexController@searchAction');
$router->get('update', 'IndexController@updateIndexAction');

$router->get('api/update', 'UpdateController@updateAction');
$router->get('api/categories/get[/{format}]', 'ApiController@categoriesSearchAction');
$router->get('api/products/get[/{format}]', 'ApiController@productsSearchAction');
$router->get('api/contact_categories/get[/{format}]', 'ApiController@contactCategoriesSearchAction');
$router->get('api/get_dollar_currency', 'ApiController@getDollarRateAction');

