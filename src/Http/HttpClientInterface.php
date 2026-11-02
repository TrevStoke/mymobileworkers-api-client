<?php

declare(strict_types=1);

namespace MyMobileWorkers\Http;

use MyMobileWorkers\Exceptions\NetworkException;

interface HttpClientInterface
{
    /**
     * Send an HTTP request and return the Response object.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $url Full request URL
     * @param array<string, string> $headers Headers key-value pairs
     * @param string|array<mixed>|null $body Request payload
     * @param array<int, mixed> $curlOptions Additional cURL options
     *
     * @return Response
     * @throws NetworkException
     */
    public function send(
        string $method,
        string $url,
        array $headers = [],
        $body = null,
        array $curlOptions = []
    ): Response;
}
