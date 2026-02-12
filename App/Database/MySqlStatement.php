<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use stdClass;

class MySqlStatement implements Statement
{
    protected MySqlDatabase $database;

    protected string $sql;

    protected ?PDOStatement $pdoStatement = null;

    public function __construct(MySqlDatabase $database, string $sql)
    {
        $this->database = $database;
        $this->sql = $sql;
    }

    function execute(array $params = []): bool
    {
        // Execute this loop once if the query succeeds.
        // Reconnect and execute this loop a second time if a disconnect is detected.
        for ($i = 0; $i < 2; $i++) {
            try {
                $this->pdoStatement = $this->database->getPdo()->prepare($this->sql);

                foreach ($params as $param => $value) {
                    $this->pdoStatement->bindValue($param, $value, match (gettype($value)) {
                        'NULL' => PDO::PARAM_NULL,
                        'boolean' => PDO::PARAM_BOOL,
                        'integer' => PDO::PARAM_INT,
                        default => PDO::PARAM_STR,
                    });
                }

                return $this->pdoStatement->execute();
            } catch (PDOException $exception) {
                if ($i === 0 && $this->database->causedByLostConnection($exception)) {
                    $this->database->connect();
                    continue;
                }

                throw $exception;
            }
        }
    }

    function fetch(string $class = stdClass::class): object|false|null
    {
        if (!$this->pdoStatement) {
            throw new Exception("Not executed!");
        }

        return $this->pdoStatement->fetchObject($class);
    }

    function fetchAll(string $class = stdClass::class): array
    {
        if (!$this->pdoStatement) {
            throw new Exception("Not executed!");
        }

        return $this->pdoStatement->fetchAll(PDO::FETCH_CLASS, $class);
    }
}
