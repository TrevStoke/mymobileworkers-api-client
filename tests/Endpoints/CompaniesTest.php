<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class CompaniesTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetCompany(): void
    {
        $this->mockHttp->addResponse(200, [], '{"company_id": 1, "name": "Highway Maintenance Ltd"}');

        $result = $this->client->companies()->get();

        $this->assertSame('Highway Maintenance Ltd', $result['name']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/companies/company?', $req['url']);
    }

    public function testCreateAttachmentFolders(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $folders = [['name' => 'Safety Documents']];
        $result = $this->client->companies()->createAttachmentFolders($folders);

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/companies/attachment_folders', $req['url']);
        $this->assertSame($folders, $req['body']['attachment_folders']);
    }
}
