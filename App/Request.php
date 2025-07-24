<?php

namespace App;

class Request
{
    /**
     * @return static
     */
    public static function capture(): static
    {
        /** @var array<string, string> $headers */
        $headers = array_reduce(array_keys($_SERVER), function (array $headers, string $key) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));

                $headers[$header] = $_SERVER[$key];
            }

            return $headers;
        }, []);

        $data = parse_url($_SERVER['REQUEST_URI']);

        return (new static())
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setUri($data['path'])
            ->setQuery($data['query'] ?? '')
            ->setHeaders($headers)
            ->setBody(file_get_contents('php://input'))
            ->setGet($_GET)
            ->setPost($_POST);
    }

    /** @var string */
    protected string $method;

    /** @var string */
    protected string $uri;

    /** @var string */
    protected string $query;

    /** @var array */
    protected array $headers;

    /** @var string */
    protected string $body;

    /** @var array */
    protected array $get;

    /** @var array */
    protected array $post;

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return self
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     * @return self
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array $get
     * @return self
     */
    public function setGet(array $get): self
    {
        $this->get = $get;
        
        return $this;
    }

    /**
     * @param array $post
     * @return self
     */
    public function setPost(array $post): self
    {
        $this->post = $post;

        return $this;
    }
    
    /**
     * @param string $name
     * @return string|null
     */
    public function header(string $name)
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * @return void
     */
    protected function populateGet(): void
    {
        parse_str($this->query, $this->get);
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key = null, mixed $default = null): mixed
    {
        if (!$this->get && $this->query) {
            $this->populateGet();
        }

        if ($key === null) {
            return $this->get;
        }

        return $this->get[$key] ?? $default;
    }

    protected function populatePost(): void
    {
        if ($this->header('Content-Type') == 'application/json') {
            $this->post = json_decode($this->body, true);
        }
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function post(string $key = null, mixed $default = null): mixed
    {
        if (!$this->post && $this->body) {
            $this->populatePost();
        }

        if ($key === null) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post($key) ?? $this->get($key) ?? $default;
    }
}
