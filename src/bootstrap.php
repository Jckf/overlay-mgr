<?php

use OverlayMgr\Router;

const ROOT_DIR = __DIR__;

require(ROOT_DIR . '/vendor/autoload.php');

require(ROOT_DIR . '/config.php');

require(ROOT_DIR . '/helpers.php');

container()->bind(Router::class, new Router());
