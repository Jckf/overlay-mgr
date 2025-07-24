<?php

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

        // TODO: Transitional code. Remove the buffer when all controllers return proper responses.
        ob_start();

        $response = $router->dispatch($request);

        $stdout = ob_get_contents();
        ob_end_clean();

        if ($response === null) {
            $response = $psrFactory->createResponse(200, 'OK')->withBody($psrFactory->createStream($stdout));
        }

        $worker->respond($response);
    } catch (\App\Exceptions\HttpException $exception) {
        $response = $psrFactory->createResponse($exception->getStatusCode(), $exception->getMessage())
            ->withHeader('Content-Type', 'application/json')
            ->withBody($psrFactory->createStream(json_encode($exception->jsonSerialize())));

        $worker->respond($response);
    } catch (Throwable $exception) {
        $worker->getWorker()->error((string) $exception);
    }
}
