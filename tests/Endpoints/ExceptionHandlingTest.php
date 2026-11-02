<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Endpoints;

use MyMobileWorkers\Client;
use MyMobileWorkers\Exceptions\ApiException;
use MyMobileWorkers\Exceptions\AuthenticationException;
use MyMobileWorkers\Exceptions\InvalidArgumentException;
use MyMobileWorkers\Exceptions\MyMobileWorkersException;
use MyMobileWorkers\Exceptions\NetworkException;
use MyMobileWorkers\Exceptions\NotFoundException;
use MyMobileWorkers\Exceptions\ValidationException;
use MyMobileWorkers\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;
use Throwable;

class ExceptionHandlingTest extends TestCase
{
    private MockHttpClient $mockHttp;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockHttp = new MockHttpClient();
        $this->client = new Client('test-api-key', $this->mockHttp);
    }

    public function testAuthenticationExceptionOn401(): void
    {
        $this->mockHttp->addResponse(401, [], '{"error": "Invalid API key"}');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');
        $this->client->jobs()->get('J123');
    }

    public function testAuthenticationExceptionOn403(): void
    {
        $this->mockHttp->addResponse(403, [], '{"error": "Access denied"}');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Access denied');
        $this->client->jobs()->get('J123');
    }

    public function testNotFoundExceptionOn404(): void
    {
        $this->mockHttp->addResponse(404, [], '{"error": "Job not found"}');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Job not found');
        $this->client->jobs()->get('J123');
    }

    public function testValidationExceptionOn400(): void
    {
        $this->mockHttp->addResponse(400, [], '{"error": "Bad request parameters", "error_code": "ERR_01", "errors": ["Field missing"]}');

        try {
            $this->client->jobs()->create([]);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertSame(400, $e->getStatusCode());
            $this->assertSame('Bad request parameters', $e->getMessage());
            $this->assertSame('ERR_01', $e->getApiErrorCode());
            $this->assertSame(['Field missing'], $e->getErrors());
            $this->assertIsArray($e->getResponseBody());
        }
    }

    public function testValidationExceptionOn422(): void
    {
        $this->mockHttp->addResponse(422, [], '{"message": "Unprocessable entity"}');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Unprocessable entity');
        $this->client->jobs()->create([]);
    }

    public function testApiExceptionOn500(): void
    {
        $this->mockHttp->addResponse(500, [], '{"error": "Internal server crash", "error_code": "SRV_500"}');

        try {
            $this->client->jobs()->get('J123');
            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(500, $e->getStatusCode());
            $this->assertSame('Internal server crash', $e->getMessage());
            $this->assertSame('SRV_500', $e->getApiErrorCode());
        }
    }

    public function testNetworkException(): void
    {
        $this->mockHttp->addException(new NetworkException('cURL error 28: Operation timeout', 28));

        try {
            $this->client->jobs()->get('J123');
            $this->fail('Expected NetworkException was not thrown');
        } catch (NetworkException $e) {
            $this->assertSame(28, $e->getCurlErrorCode());
            $this->assertStringContainsString('Operation timeout', $e->getMessage());
        }
    }

    public function testExceptionHierarchyAndInterfaceImplementation(): void
    {
        $exceptions = [
            new ApiException('API error', 500),
            new AuthenticationException('Auth error', 401),
            new NotFoundException('Not found', 404),
            new ValidationException('Validation error', 400),
            new NetworkException('Network error', 28),
            new InvalidArgumentException('Invalid argument'),
        ];

        foreach ($exceptions as $e) {
            $this->assertInstanceOf(MyMobileWorkersException::class, $e);
            $this->assertInstanceOf(Throwable::class, $e);
            $this->assertIsString((string) $e);
        }
    }
}
