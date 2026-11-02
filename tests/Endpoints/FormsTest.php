<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class FormsTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetByName(): void
    {
        $this->mockHttp->addResponse(200, [], '{"form_name": "Safety Audit", "fields": []}');

        $result = $this->client->forms()->getByName('Safety Audit');

        $this->assertSame('Safety Audit', $result['form_name']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/forms/form?', $req['url']);
        $this->assertStringContainsString('form_name=Safety+Audit', $req['url']);
    }

    public function testGetByNameAndUsername(): void
    {
        $this->mockHttp->addResponse(200, [], '{"form_name": "Safety Audit", "username": "john_doe"}');

        $result = $this->client->forms()->getByNameAndUsername('Safety Audit', 'john_doe');

        $this->assertSame('john_doe', $result['username']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/forms/form/user?', $req['url']);
        $this->assertStringContainsString('form_name=Safety+Audit', $req['url']);
        $this->assertStringContainsString('username=john_doe', $req['url']);
    }
}
