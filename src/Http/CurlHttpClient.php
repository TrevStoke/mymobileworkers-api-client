<?php

declare(strict_types=1);

namespace MyMobileWorkers\Http;

use CURLFile;
use MyMobileWorkers\Config;
use MyMobileWorkers\Exceptions\NetworkException;

class CurlHttpClient implements HttpClientInterface
{
    protected Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? new Config();
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send(
        string $method,
        string $url,
        array $headers = [],
        $body = null,
        array $curlOptions = []
    ): Response {
        $ch = curl_init();

        $mergedHeaders = array_merge($this->config->getHeaders(), $headers);
        $headerLines = [];
        $hasContentType = false;

        foreach ($mergedHeaders as $key => $value) {
            if (is_string($key)) {
                $headerLines[] = "{$key}: {$value}";
                if (strtolower($key) === 'content-type') {
                    $hasContentType = true;
                }
            } else {
                $headerLines[] = $value;
                if (stripos($value, 'content-type:') === 0) {
                    $hasContentType = true;
                }
            }
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => $this->config->getTimeout(),
            CURLOPT_CONNECTTIMEOUT => $this->config->getConnectTimeout(),
            CURLOPT_USERAGENT => $this->config->getUserAgent(),
            CURLOPT_SSL_VERIFYPEER => $this->config->getVerifySsl() ? 1 : 0,
            CURLOPT_SSL_VERIFYHOST => $this->config->getVerifySsl() ? 2 : 0,
        ];

        if ($this->config->getCaBundlePath() !== null) {
            $options[CURLOPT_CAINFO] = $this->config->getCaBundlePath();
        }

        $methodUpper = strtoupper($method);
        switch ($methodUpper) {
            case 'GET':
                $options[CURLOPT_HTTPGET] = true;
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
            case 'HEAD':
            case 'OPTIONS':
                $options[CURLOPT_CUSTOMREQUEST] = $methodUpper;
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = $methodUpper;
                break;
        }

        if ($body !== null && $methodUpper !== 'GET' && $methodUpper !== 'HEAD') {
            if (is_array($body)) {
                $isMultipart = false;
                foreach ($body as $val) {
                    if ($val instanceof CURLFile) {
                        $isMultipart = true;
                        break;
                    }
                }

                if ($isMultipart) {
                    $options[CURLOPT_POSTFIELDS] = $body;
                } else {
                    $jsonEncoded = json_encode($body);
                    $options[CURLOPT_POSTFIELDS] = $jsonEncoded;
                    if (!$hasContentType) {
                        $headerLines[] = 'Content-Type: application/json';
                    }
                }
            } else {
                $options[CURLOPT_POSTFIELDS] = $body;
                if (!$hasContentType && is_string($body) && (str_starts_with($body, '{') || str_starts_with($body, '['))) {
                    $headerLines[] = 'Content-Type: application/json';
                }
            }
        }

        if (!empty($headerLines)) {
            $options[CURLOPT_HTTPHEADER] = $headerLines;
        }

        // Apply config-level custom cURL options
        foreach ($this->config->getCurlOptions() as $optKey => $optVal) {
            $options[$optKey] = $optVal;
        }

        // Apply request-level custom cURL options
        foreach ($curlOptions as $optKey => $optVal) {
            $options[$optKey] = $optVal;
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if ($result === false) {
            $errorCode = curl_errno($ch);
            $errorMessage = curl_error($ch);
            if (PHP_VERSION_ID < 80500) {
                curl_close($ch);
            }
            throw new NetworkException(
                "cURL request error ({$errorCode}): {$errorMessage}",
                $errorCode
            );
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }

        $rawHeaders = substr((string) $result, 0, $headerSize);
        $rawBody = substr((string) $result, $headerSize);

        $parsedHeaders = $this->parseHeaders($rawHeaders);

        return new Response($statusCode, $parsedHeaders, $rawBody);
    }

    /**
     * Parse raw headers string into an associative array.
     *
     * @param string $rawHeaders
     * @return array<string, mixed>
     */
    protected function parseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $headerBlocks = explode("\r\n\r\n", trim($rawHeaders));
        $lastHeaderBlock = end($headerBlocks);

        if ($lastHeaderBlock !== false) {
            $lines = explode("\r\n", $lastHeaderBlock);
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    if (isset($headers[$key])) {
                        if (is_array($headers[$key])) {
                            $headers[$key][] = $value;
                        } else {
                            $headers[$key] = [$headers[$key], $value];
                        }
                    } else {
                        $headers[$key] = $value;
                    }
                }
            }
        }

        return $headers;
    }
}
