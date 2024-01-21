<?php

use App\Container;

/**
 * @param string|null $class
 * @param array $parameters
 * @return Container|mixed
 */
function container(string $class = null, array $parameters = []): mixed
{
    $container = Container::getInstance();

    if ($class === null) {
        return $container;
    }

    return $container->make($class, $parameters);
}
