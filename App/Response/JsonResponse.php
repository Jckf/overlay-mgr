<?php

namespace App\Response;

use JsonException;
use Psr\Http\Message\ResponseInterface;

class JsonResponse extends Response
{
    public function __construct(ResponseInterface $response = null)
    {
        parent::__construct(
            static::orDefault($response)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
        );
    }

    /**
     * @throws JsonException
     */
    public function withJson(mixed $data): static
    {
        return $this->withBody(static::createStream(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)));
    }
}
