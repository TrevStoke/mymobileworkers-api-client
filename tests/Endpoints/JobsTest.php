<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Exceptions\InvalidArgumentException;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class JobsTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testGetJob(): void
    {
        $this->mockHttp->addResponse(200, [], '{"job_number": "J123", "address_1": "123 High St"}');

        $result = $this->client->jobs()->get('J123');

        $this->assertSame('J123', $result['job_number']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/jobs/job?', $req['url']);
        $this->assertStringContainsString('job_number=J123', $req['url']);
        $this->assertStringContainsString('api_key=test-api-key', $req['url']);
    }

    public function testGetByCurrentStatus(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"job_number": "J101"}]');

        $result = $this->client->jobs()->getByCurrentStatus('NEW', '2026-01-01', '2026-01-31');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/jobs/current_status?', $req['url']);
        $this->assertStringContainsString('status=NEW', $req['url']);
        $this->assertStringContainsString('start_date=2026-01-01', $req['url']);
        $this->assertStringContainsString('end_date=2026-01-31', $req['url']);
    }

    public function testGetByActivityStatus(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"job_number": "J102"}]');

        $result = $this->client->jobs()->getByActivityStatus('IN_PROGRESS');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/jobs/activity_status?', $req['url']);
        $this->assertStringContainsString('status=IN_PROGRESS', $req['url']);
    }

    public function testGetUpdatesSince(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"job_number": "J103"}]');

        $result = $this->client->jobs()->getUpdatesSince('2026-01-01 00:00:00');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/jobs/updates/since?', $req['url']);
        $this->assertStringContainsString('since=2026-01-01+00%3A00%3A00', $req['url']);
    }

    public function testGetUpdatesBetween(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"job_number": "J104"}]');

        $result = $this->client->jobs()->getUpdatesBetween('2026-01-01', '2026-01-02');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/jobs/updates/between?', $req['url']);
        $this->assertStringContainsString('start_date=2026-01-01', $req['url']);
        $this->assertStringContainsString('end_date=2026-01-02', $req['url']);
    }

    public function testGetPhotosAddedSince(): void
    {
        $this->mockHttp->addResponse(200, [], '[{"photo_id": 1}]');

        $result = $this->client->jobs()->getPhotosAddedSince('2026-01-01 00:00:00');

        $this->assertCount(1, $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertStringContainsString('/jobs/photos/since?', $req['url']);
        $this->assertStringContainsString('since=2026-01-01+00%3A00%3A00', $req['url']);
    }

    public function testCreateJobs(): void
    {
        $this->mockHttp->addResponse(200, [], '{"successful": [{"job_number": "J999"}]}');

        $jobsData = [
            [
                'job_number' => 'J999',
                'address_1' => 'Test Road',
                'postcode' => 'TE1 1ST',
                'contact_name' => 'John Doe',
                'contact_number_1' => '07123456789',
                'type' => 'repair',
            ]
        ];

        $result = $this->client->jobs()->create($jobsData, false, true);

        $this->assertArrayHasKey('successful', $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs', $req['url']);
        $this->assertSame('false', $req['body']['validate_only']);
        $this->assertSame('true', $req['body']['update_existing']);
        $this->assertSame('test-api-key', $req['body']['key']);
        $this->assertSame($jobsData, $req['body']['jobs']);
    }

    public function testCreateFromFile(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mmw_job_');
        file_put_contents($tmpFile, '{"jobs": []}');

        $this->mockHttp->addResponse(200, [], '{"successful": []}');

        $result = $this->client->jobs()->createFromFile($tmpFile, true, false);

        $this->assertArrayHasKey('successful', $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertArrayHasKey('file', $req['body']);
        $this->assertSame('true', $req['body']['validate_only']);
        $this->assertSame('false', $req['body']['update_existing']);

        unlink($tmpFile);
    }

    public function testCreateFromFileNonExistent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->jobs()->createFromFile('/non/existent/file.json');
    }

    public function testDeleteJob(): void
    {
        $this->mockHttp->addResponse(200, [], '{"status": "deleted"}');

        $result = $this->client->jobs()->delete('J123');

        $this->assertSame(['status' => 'deleted'], $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/delete', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
    }

    public function testDeleteMultipleJobs(): void
    {
        $this->mockHttp->addResponse(200, [], '{"status": "deleted"}');

        $result = $this->client->jobs()->deleteMultiple(['J1', 'J2']);

        $this->assertSame(['status' => 'deleted'], $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/delete_jobs', $req['url']);
        $this->assertSame(['J1', 'J2'], $req['body']['jobs']);
    }

    public function testCreateItems(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $items = [['name' => 'Item 1', 'quantity' => 2]];
        $result = $this->client->jobs()->createItems('J123', $items);

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/items', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame($items, $req['body']['items']);
    }

    public function testDeleteItems(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $items = ['item-id-1'];
        $result = $this->client->jobs()->deleteItems('J123', $items);

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('DELETE', $req['method']);
        $this->assertStringContainsString('/jobs/job/items', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
    }

    public function testCreateParts(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $parts = [['part_number' => 'P123', 'quantity' => 1]];
        $result = $this->client->jobs()->createParts('J123', $parts);

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/parts', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame($parts, $req['body']['parts']);
    }

    public function testGetPdf(): void
    {
        $this->mockHttp->addResponse(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4 sample content');

        $pdf = $this->client->jobs()->getPdf('J123');

        $this->assertSame('%PDF-1.4 sample content', $pdf);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('GET', $req['method']);
        $this->assertStringContainsString('/jobs/job/pdf?', $req['url']);
    }

    public function testEmailPdf(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->jobs()->emailPdf('J123', 'client@example.com');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/pdf/email', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame('client@example.com', $req['body']['email']);
    }

    public function testSetStatus(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->jobs()->setStatus('J123', 'COMPLETE');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/status', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame('COMPLETE', $req['body']['status']);
    }

    public function testLink(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->jobs()->link('PARENT_1', 'CHILD_2');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/link', $req['url']);
        $this->assertSame('PARENT_1', $req['body']['parent_job_number']);
        $this->assertSame('CHILD_2', $req['body']['child_job_number']);
    }

    public function testUpdateInvoicing(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->jobs()->updateInvoicing('J123', ['po_number' => 'PO999', 'invoice_number' => 'INV888']);

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/status/invoicing?', $req['url']);
        $this->assertStringContainsString('job_number=J123', $req['url']);
        $this->assertSame('PO999', $req['body']['po_number']);
        $this->assertSame('INV888', $req['body']['invoice_number']);
    }

    public function testCreateTrackingReference(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->jobs()->createTrackingReference('J123', 'TRK1000');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/jobs/job/tracking', $req['url']);
        $this->assertSame('J123', $req['body']['job_number']);
        $this->assertSame('TRK1000', $req['body']['tracking_reference']);
    }
}
