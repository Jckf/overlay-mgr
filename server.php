<?php

use App\Exceptions\HttpException;
use App\Exceptions\HttpResponseException;
use Spiral\RoadRunner;
use Nyholm\Psr7;

use App\Request;
use App\Router;

require('bootstrap/bootstrap.php');

$container = container();

/** @var Router $router */
$router = $container->bind(Router::class, $container->make(Router::class));

require('routes.php');

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while ($req = $worker->waitRequest()) {
    try {
        /** @var Request $request */
        $request = $container->bind(Request::class, Request::capture());

        $response = $router->dispatch($request);

        $worker->respond($response);
    } catch (HttpResponseException $exception) {
        $worker->respond($exception->getResponse());
    } catch (HttpException $exception) {
        $worker->respond(response()->json($exception, $exception->getCode() ?: \App\HttpStatusCodes::INTERNAL_SERVER_ERROR));
    } catch (Throwable $throwable) {
        $worker->getWorker()->error((string) $throwable);
    }
}
