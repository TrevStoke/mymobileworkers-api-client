<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Mock;

use MyMobileWorkers\Http\HttpClientInterface;
use MyMobileWorkers\Http\Response;

class MockHttpClient implements HttpClientInterface
{
    /**
     * @var array<int, array{method: string, url: string, headers: array, body: mixed, curlOptions: array}>
     */
    public array $requests = [];

    /**
     * @var array<int, Response|\Throwable>
     */
    public array $responses = [];

    public function addResponse(int $statusCode = 200, array $headers = [], string $rawBody = '{"success": true}'): self
    {
        $this->responses[] = new Response($statusCode, $headers, $rawBody);
        return $this;
    }

    public function addResponseObject(Response $response): self
    {
        $this->responses[] = $response;
        return $this;
    }

    public function addException(\Throwable $exception): self
    {
        $this->responses[] = $exception;
        return $this;
    }

    public function getLastRequest(): ?array
    {
        return empty($this->requests) ? null : end($this->requests);
    }

    public function send(
        string $method,
        string $url,
        array $headers = [],
        $body = null,
        array $curlOptions = []
    ): Response {
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
            'curlOptions' => $curlOptions,
        ];

        if (empty($this->responses)) {
            return new Response(200, [], '{"success": true}');
        }

        $next = array_shift($this->responses);
        if ($next instanceof \Throwable) {
            throw $next;
        }

        return $next;
    }
}
