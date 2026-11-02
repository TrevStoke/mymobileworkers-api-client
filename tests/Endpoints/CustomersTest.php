<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

class CustomersTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testCreateCustomer(): void
    {
        $this->mockHttp->addResponse(200, [], '{"customer_id": 123}');

        $result = $this->client->customers()->create(['name' => 'Acme Inc']);

        $this->assertSame(123, $result['customer_id']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/customers', $req['url']);
        $this->assertSame('Acme Inc', $req['body']['name']);
    }

    public function testUpdateName(): void
    {
        $this->mockHttp->addResponse(200, [], '{"success": true}');

        $result = $this->client->customers()->updateName('Old Name', 'New Name');

        $this->assertTrue($result['success']);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/customers/customer/name', $req['url']);
        $this->assertSame('Old Name', $req['body']['current_name']);
        $this->assertSame('New Name', $req['body']['new_name']);
    }

    public function testCreateSites(): void
    {
        $this->mockHttp->addResponse(200, [], '{"successful": []}');

        $sites = [['name' => 'Site A', 'address_1' => 'Road A']];
        $result = $this->client->customers()->createSites($sites, true, false);

        $this->assertArrayHasKey('successful', $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/customers/customer/sites', $req['url']);
        $this->assertSame('true', $req['body']['validate_only']);
        $this->assertSame('false', $req['body']['update_existing']);
        $this->assertSame($sites, $req['body']['customer_sites']);
    }

    public function testCreateContacts(): void
    {
        $this->mockHttp->addResponse(200, [], '{"successful": []}');

        $contacts = [['contact_name' => 'Steve G', 'address_1' => 'Unit 5']];
        $result = $this->client->customers()->createContacts($contacts, false, true);

        $this->assertArrayHasKey('successful', $result);
        $req = $this->mockHttp->getLastRequest();
        $this->assertSame('POST', $req['method']);
        $this->assertStringContainsString('/customers/customer/contacts', $req['url']);
        $this->assertSame('false', $req['body']['validate_only']);
        $this->assertSame('true', $req['body']['update_existing']);
        $this->assertSame($contacts, $req['body']['customer_contacts']);
    }
}
