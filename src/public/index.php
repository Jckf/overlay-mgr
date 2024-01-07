<?php

use OverlayMgr\Router;

require('../bootstrap.php');

/** @var Router $router */
$router = container(Router::class);

$router->bind('get', '^\/$', function () {
    return 'Hello, World!';
});

$router->dispatch();
