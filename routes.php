<?php

use App\Controllers\ItemBidController;
use App\Controllers\ItemController;
use App\Controllers\SmsController;
use App\Router;

/** @var Router $router */
$router = container(Router::class);

// SMS
$router->bind('get', '/sms/incoming/' . env('SMS_SECRET', ''), [ SmsController::class, 'incoming' ]);

// Items
$router->bind('get', '/items/', [ ItemController::class, 'index' ]);
$router->bind('get', '/items/{item}', [ ItemController::class, 'get' ]);

// Item bids
$router->bind('get', '/items/{item}/bids/', [ ItemBidController::class, 'bidIndex' ]);
