<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', 'IndexController@indexAction');
$router->get('search', 'IndexController@searchAction');
$router->get('update', 'IndexController@updateIndexAction');

$router->get('api/update', 'UpdateController@updateAction');
$router->get('api/products/get', 'ApiController@productsSearchAction');
$router->get('api/categories/get', 'ApiController@categoriesSearchAction');
$router->get('api/get_dollar_currency', 'ApiController@getDollarRateAction');
$router->get('api/contact_categories/get', 'ApiController@contactCategoriesSearchAction');
