<?php

use App\Request;
use App\Router;

require('../bootstrap/bootstrap.php');

$container = container();

/** @var Router $router */
$router = $container->bind(Router::class, $container->make(Router::class));

require('../routes.php');

/** @var Request $request */
$request = $container->bind(Request::class, Request::capture());

$router->dispatch($request);
