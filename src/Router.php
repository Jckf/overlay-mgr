<?php

namespace OverlayMgr;

class Router
{
    protected array $routes = [];

    public function bind(string $method, string $pattern, callable $handler)
    {
        $method = strtolower($method);

        if (!array_key_exists($method, $this->routes)) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$pattern][] = $handler;
    }

    public function dispatch()
    {

    }
}
