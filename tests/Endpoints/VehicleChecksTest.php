<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class VehicleChecksTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetByRegistration(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"registration": "AB12 CDE", "passed": true}]');

        $result = $this->client->vehicleChecks()->getByRegistration('AB12 CDE', ['limit' => 10]);

        $this->assertCount(1, $result);
        $this->assertSame('AB12 CDE', $result[0]['registration']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/vehicle_checks/registration?', $req['url']);
        $this->assertStringContainsString('registration=AB12+CDE', $req['url']);
        $this->assertStringContainsString('limit=10', $req['url']);
    }
}
