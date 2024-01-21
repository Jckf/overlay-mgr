<?php

namespace App;

use App\Exceptions\HttpException;

class Router
{
    protected array $routes = [];

    public function bind(string $method, string $pattern, callable $handler)
    {
        $method = strtolower($method);

        if (!array_key_exists($pattern, $this->routes)) {
            $this->routes[$pattern] = [];
        }

        $this->routes[$pattern][$method] = $handler;
    }

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
