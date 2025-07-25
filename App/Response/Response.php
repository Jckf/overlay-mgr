<?php

namespace App\Response;

use Nyholm\Psr7\Response as NyholmResponse;
use Nyholm\Psr7\Stream as NyholmStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    public static function create(ResponseInterface $response = null): static
    {
        return new static($response);
    }

    public static function orDefault(ResponseInterface $response = null): ResponseInterface
    {
        return $response ?? new NyholmResponse();
    }

    /**
     * @param string|resource|StreamInterface $body
     * @return StreamInterface
     */
    public static function createStream(mixed $body): StreamInterface
    {
        return NyholmStream::create($body);
    }

    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response = null)
    {
        $this->response = static::orDefault($response);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function withResponse(ResponseInterface $response): static
    {
        $new = clone $this;

        $new->response = $response;

        return $new;
    }

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): static
    {
        return $this->withResponse($this->response->withProtocolVersion($version));
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): static
    {
        return $this->withResponse($this->response->withHeader($name, $value));
    }

    public function withAddedHeader(string $name, $value): static
    {
        return $this->withResponse($this->response->withAddedHeader($name, $value));
    }

    public function withoutHeader(string $name): static
    {
        return $this->withResponse($this->response->withoutHeader($name));
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body): static
    {
        return $this->withResponse($this->response->withBody($body));
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return $this->withResponse($this->response->withStatus($code, $reasonPhrase));
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }
}
