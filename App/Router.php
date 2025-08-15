<?php

namespace App;

use App\Controllers\Controller;
use App\Exceptions\HttpException;
use Exception;

class Router
{
    /** @var array */
    protected array $routes = [];

    /** @var array<class-string, Controller> */
    protected array $controllers = [];

    /**
     * @param string $method
     * @param string $path
     * @param $handler
     * @return void
     * @throws Exception
     */
    public function bind(string $method, string $path, $handler)
    {
        $method = strtolower($method);

        if (is_array($handler) && is_string($handler[0])) {
            $handler[0] = $this->controllers[$handler[0]] ?? $this->controllers[$handler[0]] = container($handler[0]);
        }

        if (!is_callable($handler)) {
            throw new Exception('Handler must be callable.');
        }

        $variables = [];

        $branch = &$this->routes;

        foreach (explode('/', $path) as $part) {
            if (substr($part, 0, 1) == '{') {
                $variables[] = substr($part, 1, -1);
                $part = '$';
            }

            if (!array_key_exists($part, $branch)) {
                $branch[$part] = [];
            }

            $branch = &$branch[$part];
        }

        $branch[$method] = [
            'handler' => $handler,
            'variables' => $variables,
        ];
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws HttpException
     */
    public function dispatch(Request $request)
    {
        $method = strtolower($request->getMethod());

        $variables = [];

        $branch = &$this->routes;

        foreach (explode('/', $request->getUri()) as $part) {
            if (!array_key_exists($part, $branch)) {
                if (!array_key_exists('$', $branch)) {
                    break;
                }

                $variables[] = $part;
                $part = '$';
            }

            $branch = &$branch[$part];
        }

        $handler = $branch[$method]['handler'] ?? null;

        // TODO: Method not allowed.
        if (!is_callable($handler)) {
            throw new HttpException(HttpStatusCodes::NOT_FOUND, 'Not found.');
        }

        // TODO: Match variables with method parameters.
        return $handler($request, ... $variables);
    }
}
