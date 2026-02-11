<?php

declare(strict_types=1);

namespace App\Database;

interface Database
{
    function connect(): void;

    function disconnect(): void;

    function prepare(string $sql): Statement;

    function lastInsertId(): string|false;
}
