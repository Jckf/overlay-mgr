<?php

namespace App\Exceptions;

use Exception;
use JsonSerializable;

class HttpException extends Exception implements JsonSerializable
{
    /** @var int */
    protected int $statusCode;

    /**
     * @param int $statusCode
     * @param string $message
     */
    public function __construct(int $statusCode, string $message)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'status_code' => $this->getStatusCode(),
            'message' => $this->getMessage(),
            'stack_trace' => $this->getTrace(),
        ];
    }
}
