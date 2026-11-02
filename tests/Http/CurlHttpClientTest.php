<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests\Http;

use MyMobileWorkers\Config;
use MyMobileWorkers\Exceptions\NetworkException;
use MyMobileWorkers\Http\CurlHttpClient;
use PHPUnit\Framework\TestCase;

class CurlHttpClientTest extends TestCase
{
    public function testInstantiationAndConfig(): void
    {
        $config = new Config('api-key', 'https://api.test.com');
        $client = new CurlHttpClient($config);

        $this->assertSame($config, $client->getConfig());

        $newConfig = new Config('new-key');
        $client->setConfig($newConfig);
        $this->assertSame($newConfig, $client->getConfig());
    }

    public function testSendWithInvalidUrlThrowsNetworkException(): void
    {
        $client = new CurlHttpClient(new Config('key', 'http://invalid-non-existent-domain-12345.xyz', 1, 1));

        $this->expectException(NetworkException::class);
        $client->send('GET', 'http://invalid-non-existent-domain-12345.xyz/api/test');
    }
}
