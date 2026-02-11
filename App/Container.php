<?php

namespace App;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use stdClass;

class Container
{
    /** @var static|null */
    protected static ?self $instance = null;

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /** @var array<string, class-string> */
    protected array $aliases = [];

    /**
     * @template T
     * @var array<class-string<T>, T>
     */
    protected array $instances = [];

    /**
     * @param string $alias
     * @param string $class
     * @return void
     */
    public function setAlias(string $alias, string $class)
    {
        $this->aliases[$alias] = $class;
    }

    /**
     * @param string $classOrAlias
     * @return string
     */
    public function getClass(string $classOrAlias): string
    {
        return $this->aliases[$classOrAlias] ?? $classOrAlias;
    }

    /**
     * @template T
     * @param class-string<T> $class Class name or scalar type.
     * @param array $parameters
     * @return T|mixed
     */
    public function make(string $class, array $parameters = [])
    {
        switch ($class) {
            case 'string': return '';
            case 'int': return 0;
            case 'float': return 0.0;
            case 'bool': return false;
            case 'array': return [];
            case 'object': return new stdClass();
        }

        $class = $this->getClass($class);

        if (array_key_exists($class, $this->instances)) {
            if (is_callable($this->instances[$class])) {
                return $this->instances[$class]();
            }

            return $this->instances[$class];
        }

        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $constructorParameters = $constructor->getParameters();

        $parameters = array_map(function (ReflectionParameter $parameter) use ($parameters) {
            if (array_key_exists($parameter->getName(), $parameters)) {
                return $parameters[$parameter->getName()];
            }

            $type = $parameter->getType();

            if ($type === null || $type->allowsNull()) {
                return null;
            }

            return $this->make($parameter->getType()->getName());
        }, $constructorParameters);

        return new $class(... $parameters);
    }

    /**
     * @template T
     * @param class-string|class-string[] $class
     * @param object<T> $instance
     * @return T
     */
    public function bind(string|array $class, object $instance)
    {
        foreach ((array) $class as $abstract) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * @param class-string $class
     */
    public function unbind(string $class)
    {
        unset($this->instances[$class]);
    }
}
