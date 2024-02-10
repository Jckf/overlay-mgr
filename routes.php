<?php

use App\Controllers\ItemController;
use App\Controllers\SmsController;
use App\Request;
use App\Router;

/** @var Router $router */
$router = container(Router::class);

$router->bind('get', '/^\/$/', function (Request $request) {
    header('Location: /static/');
});

// SMS
$router->bind('get', '/^\/api\/sms\/incoming$/', [ SmsController::class, 'incoming' ]);

// Items
$router->bind('get', '/^\/api\/items\/$/', [ ItemController::class, 'index' ]);
$router->bind('get', '/^\/api\/items\/(.+)$/', [ ItemController::class, 'get' ]);
