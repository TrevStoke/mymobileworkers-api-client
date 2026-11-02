<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class AssetsTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testCreateAssets(): void
    {
        $this->mockHttp->addResponse(200, [], '{"successful": [{"serial_number": "SN123"}]}');

        $assets = [['serial_number' => 'SN123', 'name' => 'Generator']];
        $result = $this->client->assets()->create($assets, true, false);

        $this->assertArrayHasKey('successful', $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/assets', $req['url']);
        $this->assertSame('true', $req['body']['validate_only']);
        $this->assertSame('false', $req['body']['update_existing']);
        $this->assertSame($assets, $req['body']['assets']);
    }

    public function testGetBySerialNumber(): void
    {
        $this->mockHttp->addResponse(200, [], '{"serial_number": "SN123"}');

        $result = $this->client->assets()->getBySerialNumber('SN123');

        $this->assertSame('SN123', $result['serial_number']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/assets/asset?', $req['url']);
        $this->assertStringContainsString('serial_number=SN123', $req['url']);
    }

    public function testGetUpdatedSince(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"serial_number": "SN123"}]');

        $result = $this->client->assets()->getUpdatedSince('2026-01-01');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/assets/updates/since?', $req['url']);
        $this->assertStringContainsString('since=2026-01-01', $req['url']);
    }

    public function testGetAssignedToCustomer(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"serial_number": "SN123"}]');

        $result = $this->client->assets()->getAssignedToCustomer('Acme Corp');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/assets/customer?', $req['url']);
        $this->assertStringContainsString('customer=Acme+Corp', $req['url']);
    }

    public function testGetByCategory(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"serial_number": "SN123"}]');

        $result = $this->client->assets()->getByCategory('Heavy Machinery');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/assets/category?', $req['url']);
        $this->assertStringContainsString('category=Heavy+Machinery', $req['url']);
    }

    public function testGetHistory(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"event": "created"}]');

        $result = $this->client->assets()->getHistory('SN123');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/assets/history?', $req['url']);
        $this->assertStringContainsString('serial_number=SN123', $req['url']);
    }

    public function testGetStatuses(): void
    {
        $this->mockHttp->addResponse(200, [], '["Available", "In Use"]');

        $result = $this->client->assets()->getStatuses();

        $this->assertSame(['Available', 'In Use'], $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/assets/statuses?', $req['url']);
    }
}
