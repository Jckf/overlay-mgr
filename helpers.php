<?php

use App\Container;

/**
 * @template T
 * @param class-string<T>|null $class
 * @param array $parameters
 * @return T|Container
 */
function container(string $class = null, array $parameters = []): mixed
{
    $container = Container::getInstance();

    if ($class === null) {
        return $container;
    }

    return $container->make($class, $parameters);
}

/**
 * @param class-string $class
 * @param array $parameters
 * @return mixed
 */
function perform(string $class, array $parameters = []): mixed
{
    return container($class, $parameters)();
}

/**
 * @param string $value
 * @return string
 */
function snake_case(string $value): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
}

/**
 * @param string $value
 * @return string
 */
function camel_case(string $value): string
{
    return lcfirst(str_replace('_', '', ucwords($value, '_')));
}

/**
 * @return int
 */
function millitime(): int
{
    return (int) (microtime(true) * 1000);
}

/**
 * @param string $key
 * @param string|null $default
 * @return string|null
 */
function env(string $key, string $default = null): ?string
{
    return $_ENV[$key] ?? $default;
}
