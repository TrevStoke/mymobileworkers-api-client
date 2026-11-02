<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Http;

use MyMobileWorkers\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testJsonResponse(): void
    {
        $headers = ['Content-Type' => 'application/json'];
        $body = '{"job_number": "J12345", "status": "COMPLETE"}';
        $response = new Response(200, $headers, $body);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isSuccess());
        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame('application/json', $response->getHeader('content-type'));
        $this->assertSame('application/json', $response->getHeader('CONTENT-TYPE'));
        $this->assertNull($response->getHeader('non-existent'));
        $this->assertSame($body, $response->getRawBody());
        $this->assertSame($body, $response->getBody());
        $this->assertSame(['job_number' => 'J12345', 'status' => 'COMPLETE'], $response->getData());
        $this->assertSame(['job_number' => 'J12345', 'status' => 'COMPLETE'], $response->json());
        $this->assertSame(['job_number' => 'J12345', 'status' => 'COMPLETE'], $response->toArray());
    }

    public function testNonJsonAndEmptyResponse(): void
    {
        $response = new Response(204, [], '');
        $this->assertSame(204, $response->getStatusCode());
        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getData());
        $this->assertSame([], $response->toArray());

        $pdfResponse = new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4 binary data');
        $this->assertSame('%PDF-1.4 binary data', $pdfResponse->getRawBody());
        $this->assertSame('%PDF-1.4 binary data', $pdfResponse->getData());
        $this->assertSame([], $pdfResponse->toArray());

        $errorResponse = new Response(500, [], '{"error": "Internal Server Error"}');
        $this->assertFalse($errorResponse->isSuccess());
    }
}
