<?php

namespace App\Response;

use App\HttpStatusCodes;
use JsonException;

class ResponseFactory
{
    public function createResponse(string $body = null, int $status = HttpStatusCodes::OK, array $headers = []): Response
    {
        $response = Response::create()->withStatus($status);

        if ($body !== null) {
            $response = $response->withBody(Response::createStream($body));
        }

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    public function redirect(string $url, int $status = HttpStatusCodes::FOUND, array $headers = []): Response
    {
        return $this->createResponse(null, $status, $headers)
            ->withHeader('Location', $url);
    }

    /**
     * @throws JsonException
     */
    public function json(mixed $data, int $status = HttpStatusCodes::OK, array $headers = []): JsonResponse
    {
        $response = JsonResponse::create()
            ->withStatus($status)
            ->withJson($data);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
