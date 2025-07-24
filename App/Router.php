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
     * @param string $pattern
     * @param $handler
     * @return void
     * @throws Exception
     */
    public function bind(string $method, string $pattern, $handler)
    {
        $method = strtolower($method);

        if (!array_key_exists($pattern, $this->routes)) {
            $this->routes[$pattern] = [];
        }

        if (is_array($handler) && is_string($handler[0])) {
            $handler[0] = $this->controllers[$handler[0]] ?? $this->controllers[$handler[0]] = container($handler[0]);
        }

        if (!is_callable($handler)) {
            throw new Exception('Handler must be callable.');
        }

        $this->routes[$pattern][$method] = $handler;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws HttpException
     */
    public function dispatch(Request $request)
    {
        $foundMatch = false;

        foreach ($this->routes as $pattern => $methods) {
            if (preg_match($pattern, $request->getUri(), $matches)) {
                array_shift($matches);

                $foundMatch = true;

                $method = strtolower($request->getMethod());

                if (array_key_exists($method, $methods)) {
                    $handler = $methods[$method];

                    return $handler($request, ...$matches);
                }
            }
        }

        if ($foundMatch) {
            throw new HttpException(HttpStatusCodes::METHOD_NOT_ALLOWED, 'Method not allowed.');
        }

        throw new HttpException(HttpStatusCodes::NOT_FOUND, 'Not found.');
    }
}
