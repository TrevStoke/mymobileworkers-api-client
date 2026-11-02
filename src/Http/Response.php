<?php

declare(strict_types=1);

namespace MyMobileWorkers\Http;

class Response
{
    protected int $statusCode;
    protected array $headers;
    protected string $rawBody;
    protected $data;

    public function __construct(int $statusCode, array $headers, string $rawBody)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->rawBody = $rawBody;

        if ($rawBody !== '') {
            $decoded = json_decode($rawBody, true);
            $this->data = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $rawBody;
        } else {
            $this->data = null;
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        $nameLower = strtolower($name);
        foreach ($this->headers as $headerName => $headerValue) {
            if (strtolower($headerName) === $nameLower) {
                return is_array($headerValue) ? implode(', ', $headerValue) : (string) $headerValue;
            }
        }

        return null;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    public function getBody(): string
    {
        return $this->rawBody;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function json()
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return is_array($this->data) ? $this->data : [];
    }
}
