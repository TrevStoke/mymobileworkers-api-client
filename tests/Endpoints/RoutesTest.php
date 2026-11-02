<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetAll(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"route_code": "R1"}]');

        $routes = $this->client->routes()->all();

        $this->assertCount(1, $routes);
        $this->assertSame('R1', $routes[0]['route_code']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/routes?', $req['url']);
    }

    public function testGetAllAlias(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"route_code": "R1"}]');

        $routes = $this->client->routes()->getAll();

        $this->assertCount(1, $routes);
    }

    public function testGetJobs(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"job_number": "J1"}]');

        $result = $this->client->routes()->getJobs('ROUTE_NORTH');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/routes/route/jobs?', $req['url']);
        $this->assertStringContainsString('route_code=ROUTE_NORTH', $req['url']);
    }

    public function testGetWeight(): void
    {
        $this->mockHttp->addResponse(200, [], '{"weight": 250.5}');

        $result = $this->client->routes()->getWeight('ROUTE_NORTH');

        $this->assertSame(250.5, $result['weight']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/routes/route/weight?', $req['url']);
        $this->assertStringContainsString('route_code=ROUTE_NORTH', $req['url']);
    }

    public function testGetLimit(): void
    {
        $this->mockHttp->addResponse(200, [], '{"limit": 500.0}');

        $result = $this->client->routes()->getLimit('ROUTE_NORTH');

        $this->assertSame(500.0, $result['limit']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/routes/route/limit?', $req['url']);
        $this->assertStringContainsString('route_code=ROUTE_NORTH', $req['url']);
    }
}
