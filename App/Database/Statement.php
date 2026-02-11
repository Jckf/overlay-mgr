<?php

declare(strict_types=1);

namespace App\Database;

use stdClass;

interface Statement
{
    function execute(array $params = []): bool;

    /**
     * @template T
     * @param class-string<T> $class
     * @return T|null
     */
    function fetch(string $class = stdClass::class): ?object;

    /**
     * @template T
     * @param class-string<T> $class
     * @return T[]
     */
    function fetchAll(string $class = stdClass::class): array;
}
