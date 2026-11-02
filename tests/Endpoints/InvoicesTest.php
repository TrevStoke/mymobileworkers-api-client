<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class InvoicesTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetUpdatedSince(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"invoice_number": "INV1"}]');

        $result = $this->client->invoices()->getUpdatedSince('2026-01-01');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/invoices/updates/since?', $req['url']);
        $this->assertStringContainsString('since=2026-01-01', $req['url']);
    }

    public function testGetUpdatedBetween(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"invoice_number": "INV1"}]');

        $result = $this->client->invoices()->getUpdatedBetween('2026-01-01', '2026-01-31');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/invoices/updates/between?', $req['url']);
        $this->assertStringContainsString('start_date=2026-01-01', $req['url']);
        $this->assertStringContainsString('end_date=2026-01-31', $req['url']);
    }

    public function testGetCreatedSince(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"invoice_number": "INV2"}]');

        $result = $this->client->invoices()->getCreatedSince('2026-01-01');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/invoices/created/since?', $req['url']);
        $this->assertStringContainsString('since=2026-01-01', $req['url']);
    }

    public function testGetCreatedBetween(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"invoice_number": "INV2"}]');

        $result = $this->client->invoices()->getCreatedBetween('2026-01-01', '2026-01-31');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/invoices/created/between?', $req['url']);
        $this->assertStringContainsString('start_date=2026-01-01', $req['url']);
        $this->assertStringContainsString('end_date=2026-01-31', $req['url']);
    }
}
