<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', 'IndexController@indexAction');
$router->get('search', 'IndexController@searchAction');
$router->get('update', 'IndexController@updateIndexAction');

/** Old Api */
$router->get('api/update', 'UpdateController@updateAction');
$router->get('api/products/get', 'ApiController@productsAction');
$router->get('api/get_dollar_currency', 'ApiController@rateAction');
$router->get('api/categories/get', 'ApiController@categoriesAction');
$router->get('api/contact_categories/get', 'ApiController@contactCategoriesAction');
/** End Old Api */

/** New Api */
$router->get('api/v2/products/get', 'NewApiController@productsAction');
$router->get('api/v2/categories/get', 'NewApiController@categoriesAction');
$router->get('api/v2/customer-groups/get', 'NewApiController@customerGroupsAction');
/** End New Api */
