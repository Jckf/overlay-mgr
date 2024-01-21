<?php

use App\Request;
use App\Router;

/** @var Router $router */
$router = container(Router::class);

$router->bind('get', '/^\/(.*)?$/', function (Request $request, string $name = null) {
    echo 'Hello, ' . ($name ? $name : 'world') . '!';
});
