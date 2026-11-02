<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class MobileUsersTest extends TestCase
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
        $this->mockHttp->addResponse(200, [], '[{"username": "john_doe"}]');

        $users = $this->client->mobileUsers()->all();

        $this->assertCount(1, $users);
        $this->assertSame('john_doe', $users[0]['username']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/mobile_users?', $req['url']);
    }

    public function testGetAllAlias(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"username": "john_doe"}]');

        $users = $this->client->mobileUsers()->getAll();

        $this->assertCount(1, $users);
    }

    public function testCreateMobileUser(): void
    {
        $this->mockHttp->addResponse(200, [], '{"username": "john_doe", "status": "active"}');

        $userData = [
            'username' => 'john_doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => 'secret123',
        ];

        $result = $this->client->mobileUsers()->create($userData);

        $this->assertSame('john_doe', $result['username']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/mobile_users', $req['url']);
        $this->assertSame('john_doe', $req['body']['username']);
    }

    public function testGetVehicleAssignment(): void
    {
        $this->mockHttp->addResponse(200, [], '{"registration": "AB12 CDE"}');

        $result = $this->client->mobileUsers()->getVehicleAssignment('john_doe', '2026-01-01');

        $this->assertSame('AB12 CDE', $result['registration']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/mobile_users/vehicle_assignment?', $req['url']);
        $this->assertStringContainsString('username=john_doe', $req['url']);
        $this->assertStringContainsString('date=2026-01-01', $req['url']);
    }

    public function testGetLocation(): void
    {
        $this->mockHttp->addResponse(200, [], '{"lat": 53.4808, "lng": -2.2426}');

        $result = $this->client->mobileUsers()->getLocation('john_doe');

        $this->assertSame(53.4808, $result['lat']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/mobile_users/location?', $req['url']);
        $this->assertStringContainsString('username=john_doe', $req['url']);
    }

    public function testGetLogs(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"log_type": "SIGN_IN"}]');

        $result = $this->client->mobileUsers()->getLogs('john_doe', '2026-01-01');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/mobile_users/logs?', $req['url']);
        $this->assertStringContainsString('username=john_doe', $req['url']);
        $this->assertStringContainsString('date=2026-01-01', $req['url']);
    }
}
