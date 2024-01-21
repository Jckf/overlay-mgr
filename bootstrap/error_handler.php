<?php

use App\Exceptions\HttpException;

set_exception_handler(function (Throwable $throwable) {
    if ($throwable instanceof HttpException) {
        http_response_code($throwable->getStatusCode());
    } else {
        http_response_code(500);
    }

    if ($throwable instanceof JsonSerializable) {
        header('Content-Type: application/json');
        echo json_encode($throwable, JSON_PRETTY_PRINT);
    } else {
        echo $throwable->getMessage();
    }
});
