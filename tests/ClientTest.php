<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests;

use MyMobileWorkers\Client;
use MyMobileWorkers\Config;
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
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testInstantiationWithApiKey(): void
    {
        $client = new Client('api-key-123');
        $this->assertSame('api-key-123', $client->getConfig()->getApiKey());
    }

    public function testInstantiationWithConfig(): void
    {
        $config = new Config('api-key-456', 'https://custom-url.com/api/1');
        $client = new Client($config);
        $this->assertSame($config, $client->getConfig());
    }

    public function testEndpointAccessors(): void
    {
        $mockHttp = new MockHttpClient();
        $client = new Client('api-key', $mockHttp);

        $this->assertInstanceOf(Jobs::class, $client->jobs());
        $this->assertSame($client->jobs(), $client->jobs()); // Test caching

        $this->assertInstanceOf(Assets::class, $client->assets());
        $this->assertSame($client->assets(), $client->assets());

        $this->assertInstanceOf(MobileUsers::class, $client->mobileUsers());
        $this->assertInstanceOf(Customers::class, $client->customers());
        $this->assertInstanceOf(Routes::class, $client->routes());
        $this->assertInstanceOf(Attachments::class, $client->attachments());
        $this->assertInstanceOf(Invoices::class, $client->invoices());
        $this->assertInstanceOf(Forms::class, $client->forms());
        $this->assertInstanceOf(VehicleChecks::class, $client->vehicleChecks());
        $this->assertInstanceOf(Companies::class, $client->companies());
    }

    public function testDirectRequest(): void
    {
        $mockHttp = new MockHttpClient();
        $mockHttp->addResponse(200, [], '{"status": "ok"}');

        $client = new Client('test-key', $mockHttp);
        $response = $client->request('GET', '/custom/path', ['custom_param' => '123']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['status' => 'ok'], $response->toArray());

        $lastReq = $mockHttp->getLastRequest();
        $this->assertNotNull($lastReq);
        $this->assertSame('GET', $lastReq['method']);
        $this->assertStringContainsString('https://api.mymobileworkers.com:8181/api/1/custom/path?', $lastReq['url']);
        $this->assertStringContainsString('api_key=test-key', $lastReq['url']);
        $this->assertStringContainsString('custom_param=123', $lastReq['url']);
    }
}
