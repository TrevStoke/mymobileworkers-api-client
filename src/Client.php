<?php

declare(strict_types=1);

namespace MyMobileWorkers;

use MyMobileWorkers\Endpoints\Assets;
use MyMobileWorkers\Endpoints\Attachments;
use MyMobileWorkers\Endpoints\Companies;
use MyMobileWorkers\Endpoints\Customers;
use MyMobileWorkers\Endpoints\Forms;
use MyMobileWorkers\Endpoints\Invoices;
use MyMobileWorkers\Endpoints\Jobs;
use MyMobileWorkers\Endpoints\MobileUsers;
use MyMobileWorkers\Endpoints\Routes;
use MyMobileWorkers\Endpoints\VehicleChecks;
use MyMobileWorkers\Http\CurlHttpClient;
use MyMobileWorkers\Http\HttpClientInterface;
use MyMobileWorkers\Http\Response;

class Client
{
    protected Config $config;
    protected HttpClientInterface $httpClient;

    // Cached endpoint instances
    protected ?Jobs $jobs = null;
    protected ?Assets $assets = null;
    protected ?MobileUsers $mobileUsers = null;
    protected ?Customers $customers = null;
    protected ?Routes $routes = null;
    protected ?Attachments $attachments = null;
    protected ?Invoices $invoices = null;
    protected ?Forms $forms = null;
    protected ?VehicleChecks $vehicleChecks = null;
    protected ?Companies $companies = null;

    /**
     * @param string|Config $configOrApiKey API key string or Config instance
     * @param HttpClientInterface|null $httpClient Custom HTTP client implementation
     */
    public function __construct($configOrApiKey = '', ?HttpClientInterface $httpClient = null)
    {
        if ($configOrApiKey instanceof Config) {
            $this->config = $configOrApiKey;
        } else {
            $this->config = new Config((string) $configOrApiKey);
        }

        $this->httpClient = $httpClient ?? new CurlHttpClient($this->config);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function jobs(): Jobs
    {
        if ($this->jobs === null) {
            $this->jobs = new Jobs($this);
        }
        return $this->jobs;
    }

    public function assets(): Assets
    {
        if ($this->assets === null) {
            $this->assets = new Assets($this);
        }
        return $this->assets;
    }

    public function mobileUsers(): MobileUsers
    {
        if ($this->mobileUsers === null) {
            $this->mobileUsers = new MobileUsers($this);
        }
        return $this->mobileUsers;
    }

    public function customers(): Customers
    {
        if ($this->customers === null) {
            $this->customers = new Customers($this);
        }
        return $this->customers;
    }

    public function routes(): Routes
    {
        if ($this->routes === null) {
            $this->routes = new Routes($this);
        }
        return $this->routes;
    }

    public function attachments(): Attachments
    {
        if ($this->attachments === null) {
            $this->attachments = new Attachments($this);
        }
        return $this->attachments;
    }

    public function invoices(): Invoices
    {
        if ($this->invoices === null) {
            $this->invoices = new Invoices($this);
        }
        return $this->invoices;
    }

    public function forms(): Forms
    {
        if ($this->forms === null) {
            $this->forms = new Forms($this);
        }
        return $this->forms;
    }

    public function vehicleChecks(): VehicleChecks
    {
        if ($this->vehicleChecks === null) {
            $this->vehicleChecks = new VehicleChecks($this);
        }
        return $this->vehicleChecks;
    }

    public function companies(): Companies
    {
        if ($this->companies === null) {
            $this->companies = new Companies($this);
        }
        return $this->companies;
    }

    /**
     * Send a raw API request.
     *
     * @param string $method
     * @param string $path
     * @param array<string, mixed> $queryParams
     * @param mixed $body
     * @param array<string, string> $headers
     * @return Response
     */
    public function request(
        string $method,
        string $path,
        array $queryParams = [],
        $body = null,
        array $headers = []
    ): Response {
        $baseUrl = $this->config->getBaseUrl();
        $path = '/' . ltrim($path, '/');
        $url = $baseUrl . $path;

        if (!isset($queryParams['api_key']) && !isset($queryParams['key'])) {
            $apiKey = $this->config->getApiKey();
            if ($apiKey !== '') {
                $queryParams['api_key'] = $apiKey;
            }
        }

        if (!empty($queryParams)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($queryParams);
        }

        return $this->httpClient->send($method, $url, $headers, $body);
    }
}
