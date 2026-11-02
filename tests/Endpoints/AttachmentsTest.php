<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Exceptions\InvalidArgumentException;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class AttachmentsTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testCreateJobAttachment(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mmw_att_');
        file_put_contents($tmpFile, 'attachment file content');

        $this->mockHttp->addResponse(200, [], '{"attachment_id": "ATT_1"}');

        $result = $this->client->attachments()->createJobAttachment('J123', $tmpFile, ['description' => 'Photo of repair']);

        $this->assertSame('ATT_1', $result['attachment_id']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/attachments/job', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame('Photo of repair', $req['body']['description']);
        $this->assertArrayHasKey('file', $req['body']);

        unlink($tmpFile);
    }

    public function testCreateJobAttachmentMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->attachments()->createJobAttachment('J123', '/missing/file.png');
    }

    public function testDeleteJobAttachment(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->attachments()->deleteJobAttachment('ATT_1');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('DELETE', $req['method']);
        $this->assertStringContainsString('/attachments/job', $req['url']);
        $this->assertSame('ATT_1', $req['body']['attachment_id']);
    }

    public function testCreateCompanyAttachment(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mmw_comp_att_');
        file_put_contents($tmpFile, 'company document');

        $this->mockHttp->addResponse(200, [], '{"attachment_id": "COMP_ATT_1"}');

        $result = $this->client->attachments()->createCompanyAttachment($tmpFile, ['folder' => 'Certificates']);

        $this->assertSame('COMP_ATT_1', $result['attachment_id']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/attachments/company', $req['url']);
        $this->assertSame('Certificates', $req['body']['folder']);
        $this->assertArrayHasKey('file', $req['body']);

        unlink($tmpFile);
    }

    public function testCreateCompanyAttachmentMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->attachments()->createCompanyAttachment('/missing/file.pdf');
    }
}
