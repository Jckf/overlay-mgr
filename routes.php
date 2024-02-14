<?php

use App\Controllers\ItemBidController;
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
$router->bind('get', '/^\/api\/sms\/incoming\/' . env('SMS_SECRET', '') . '$/', [ SmsController::class, 'incoming' ]);

// Items
$router->bind('get', '/^\/api\/items\/$/', [ ItemController::class, 'index' ]);
$router->bind('get', '/^\/api\/items\/([0-9]+)$/', [ ItemController::class, 'get' ]);

// Item bids
$router->bind('get', '/^\/api\/items\/([0-9]+)\/bids\/$/', [ ItemBidController::class, 'bidIndex' ]);
