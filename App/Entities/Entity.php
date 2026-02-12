<?php

namespace App\Entities;

use Exception;
use JsonSerializable;
use ReflectionClass;

abstract class Entity implements JsonSerializable
{
    /** @var array<string, string[]> */
    protected static ?array $attributes = [];

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @return string
     */
    public function getPrimaryKeyName(): string
    {
        return 'id';
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute(string $name, mixed $value): static
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute(string $name): mixed
    {
        return $this->$name;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return isset($this->{$this->getPrimaryKeyName()});
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        if (empty(static::$attributes[static::class])) {
            static::$attributes[static::class] = [];

            $reflectionClass = new ReflectionClass($this);

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                static::$attributes[static::class][] = $reflectionProperty->getName();
            }
        }

        return static::$attributes[static::class];
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @deprecated This is intended for the ORM. Use magic methods instead.
     */
    public function __set(string $attribute, mixed $value)
    {
        $this->setAttribute(camel_case($attribute), $value);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (str_starts_with($name, 'set')) {
            $attribute = lcfirst(substr($name, 3));

            $this->setAttribute($attribute, $arguments[0] ?? null);

            return $this;
        }

        if (str_starts_with($name, 'get')) {
            $attribute = lcfirst(substr($name, 3));

            return $this->getAttribute($attribute);
        }

        throw new Exception('Method not found');
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_reduce($this->getAttributes(), function (array $json, string $attribute) {
            $json[$attribute] = $this->getAttribute($attribute);

            return $json;
        }, []);
    }
}
