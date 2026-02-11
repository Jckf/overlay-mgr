<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use PDO;
use Throwable;

class MySqlDatabase implements Database
{
    protected ?PDO $pdo = null;

    protected string $host;

    protected int $port;

    protected string $database;

    protected string $user;

    protected string $password;

    public function __construct(string $host, int $port, string $database, string $user, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
    }

    public function getPdo(): ?PDO
    {
        if (!$this->pdo) {
            $this->connect();
        }

        return $this->pdo;
    }

    public function connect(): void
    {
        $this->disconnect();

        $this->pdo = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->database}", $this->user, $this->password);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }

    public function prepare(string $sql): Statement
    {
        if (!$this->pdo) {
            $this->connect();
        }

        return new MySqlStatement($this, $sql);
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    public function causedByLostConnection(Throwable $throwable): bool
    {
        static $phrases = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'Transaction() on null',
            'Operation now in progress',
            'Connection timed out',
        ];

        foreach ($phrases as $phrase) {
            if (str_contains($throwable->getMessage(), $phrase)) {
                return true;
            }
        }

        return false;
    }
}
