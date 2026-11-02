<?php

declare(strict_types=1);

namespace MyMobileWorkers;

class Config
{
    public const DEFAULT_BASE_URL = 'https://api20.mymobileworkers.com/api/1';
    public const DEFAULT_TIMEOUT = 30;
    public const DEFAULT_CONNECT_TIMEOUT = 10;
    public const DEFAULT_USER_AGENT = 'MyMobileWorkers-PHP-Client/1.0.1';

    protected string $apiKey;
    protected string $baseUrl;
    protected int $timeout;
    protected int $connectTimeout;
    protected bool $verifySsl;
    protected ?string $caBundlePath;
    protected string $userAgent;
    protected array $headers;
    protected array $curlOptions;

    public function __construct(
        string $apiKey = '',
        string $baseUrl = self::DEFAULT_BASE_URL,
        int $timeout = self::DEFAULT_TIMEOUT,
        int $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
        bool $verifySsl = true,
        ?string $caBundlePath = null,
        string $userAgent = self::DEFAULT_USER_AGENT,
        array $headers = [],
        array $curlOptions = []
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
        $this->verifySsl = $verifySsl;
        $this->caBundlePath = $caBundlePath;
        $this->userAgent = $userAgent;
        $this->headers = $headers;
        $this->curlOptions = $curlOptions;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function setConnectTimeout(int $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function getVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function setVerifySsl(bool $verifySsl): self
    {
        $this->verifySsl = $verifySsl;
        return $this;
    }

    public function getCaBundlePath(): ?string
    {
        return $this->caBundlePath;
    }

    public function setCaBundlePath(?string $caBundlePath): self
    {
        $this->caBundlePath = $caBundlePath;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    public function setCurlOptions(array $curlOptions): self
    {
        $this->curlOptions = $curlOptions;
        return $this;
    }

    public function setCurlOption(int $option, $value): self
    {
        $this->curlOptions[$option] = $value;
        return $this;
    }
}
