<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Config;
use MyMobileWorkers\Exceptions\ApiException;
use MyMobileWorkers\Exceptions\AuthenticationException;
use MyMobileWorkers\Exceptions\NotFoundException;
use MyMobileWorkers\Exceptions\ValidationException;
use MyMobileWorkers\Http\HttpClientInterface;
use MyMobileWorkers\Http\Response;

abstract class AbstractEndpoint
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function getClient(): Client
    {
        return $this->client;
    }

    protected function getConfig(): Config
    {
        return $this->client->getConfig();
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->client->getHttpClient();
    }

    /**
     * Build full URL with query parameters including api_key if needed.
     *
     * @param string $path
     * @param array<string, mixed> $queryParams
     * @return string
     */
    protected function buildUrl(string $path, array $queryParams = []): string
    {
        $baseUrl = $this->getConfig()->getBaseUrl();
        $path = '/' . ltrim($path, '/');
        $url = $baseUrl . $path;

        if (!isset($queryParams['api_key']) && !isset($queryParams['key'])) {
            $apiKey = $this->getConfig()->getApiKey();
            if ($apiKey !== '') {
                $queryParams['api_key'] = $apiKey;
            }
        }

        if (!empty($queryParams)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * @param string $path
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return Response
     */
    protected function httpGet(string $path, array $queryParams = [], array $headers = []): Response
    {
        $url = $this->buildUrl($path, $queryParams);
        $response = $this->getHttpClient()->send('GET', $url, $headers);
        return $this->handleResponse($response);
    }

    /**
     * @param string $path
     * @param mixed $body
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return Response
     */
    protected function httpPost(string $path, $body = null, array $queryParams = [], array $headers = []): Response
    {
        // If body is array and key is not set, inject key if apiKey exists in config
        if (is_array($body) && !isset($body['key']) && !isset($body['api_key'])) {
            $apiKey = $this->getConfig()->getApiKey();
            if ($apiKey !== '') {
                $body['key'] = $apiKey;
            }
        }

        $url = $this->buildUrl($path, $queryParams);
        $response = $this->getHttpClient()->send('POST', $url, $headers, $body);
        return $this->handleResponse($response);
    }

    /**
     * Send multipart form data (e.g. file upload).
     *
     * @param string $path
     * @param array<string, mixed> $postFields
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return Response
     */
    protected function httpPostMultipart(string $path, array $postFields, array $queryParams = [], array $headers = []): Response
    {
        $url = $this->buildUrl($path, $queryParams);
        $response = $this->getHttpClient()->send('POST', $url, $headers, $postFields);
        return $this->handleResponse($response);
    }

    /**
     * @param string $path
     * @param mixed $body
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return Response
     */
    protected function httpDelete(string $path, $body = null, array $queryParams = [], array $headers = []): Response
    {
        $url = $this->buildUrl($path, $queryParams);
        $response = $this->getHttpClient()->send('DELETE', $url, $headers, $body);
        return $this->handleResponse($response);
    }

    /**
     * @param string $path
     * @param mixed $body
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return Response
     */
    protected function httpPut(string $path, $body = null, array $queryParams = [], array $headers = []): Response
    {
        $url = $this->buildUrl($path, $queryParams);
        $response = $this->getHttpClient()->send('PUT', $url, $headers, $body);
        return $this->handleResponse($response);
    }

    /**
     * Process response and throw appropriate exceptions on failure.
     *
     * @param Response $response
     * @return Response
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws ApiException
     */
    protected function handleResponse(Response $response): Response
    {
        $statusCode = $response->getStatusCode();
        $data = $response->getData();

        if ($statusCode === 401 || $statusCode === 403) {
            $msg = is_array($data) && isset($data['error']) ? (string) $data['error'] : 'Unauthorized: Invalid or missing API key';
            throw new AuthenticationException($msg, $statusCode, $data);
        }

        if ($statusCode === 404) {
            $msg = is_array($data) && isset($data['error']) ? (string) $data['error'] : 'Resource not found';
            throw new NotFoundException($msg, $statusCode, $data);
        }

        if ($statusCode === 400 || $statusCode === 422) {
            $msg = 'Validation error';
            $errors = [];
            $errorCode = null;
            if (is_array($data)) {
                if (isset($data['error'])) {
                    $msg = (string) $data['error'];
                } elseif (isset($data['message'])) {
                    $msg = (string) $data['message'];
                }
                if (isset($data['error_code'])) {
                    $errorCode = (string) $data['error_code'];
                }
                if (isset($data['errors']) && is_array($data['errors'])) {
                    $errors = $data['errors'];
                }
            }
            throw new ValidationException($msg, $statusCode, $data, $errorCode, $errors);
        }

        if (!$response->isSuccess()) {
            $msg = "API request failed with HTTP {$statusCode}";
            $errorCode = null;
            if (is_array($data)) {
                if (isset($data['error'])) {
                    $msg = (string) $data['error'];
                } elseif (isset($data['message'])) {
                    $msg = (string) $data['message'];
                }
                if (isset($data['error_code'])) {
                    $errorCode = (string) $data['error_code'];
                }
            }
            throw new ApiException($msg, $statusCode, $data, $errorCode);
        }

        return $response;
    }
}
