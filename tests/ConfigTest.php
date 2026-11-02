<?php

declare(strict_types=1);

namespace MyMobileWorkers\Tests;

use MyMobileWorkers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $config = new Config();

        $this->assertSame('', $config->getApiKey());
        $this->assertSame(Config::DEFAULT_BASE_URL, $config->getBaseUrl());
        $this->assertSame(30, $config->getTimeout());
        $this->assertSame(10, $config->getConnectTimeout());
        $this->assertTrue($config->getVerifySsl());
        $this->assertNull($config->getCaBundlePath());
        $this->assertSame(Config::DEFAULT_USER_AGENT, $config->getUserAgent());
        $this->assertSame([], $config->getHeaders());
        $this->assertSame([], $config->getCurlOptions());
    }

    public function testCustomValuesAndSetters(): void
    {
        $config = new Config('my-secret-key');
        $this->assertSame('my-secret-key', $config->getApiKey());

        $config->setApiKey('new-key')
            ->setBaseUrl('https://custom.api.com:8181/api/1/')
            ->setTimeout(60)
            ->setConnectTimeout(20)
            ->setVerifySsl(false)
            ->setCaBundlePath('/path/to/cacert.pem')
            ->setUserAgent('CustomAgent/2.0')
            ->setHeaders(['X-Custom' => 'HeaderValue'])
            ->addHeader('X-Second', 'Value2')
            ->setCurlOptions([CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4])
            ->setCurlOption(CURLOPT_VERBOSE, true);

        $this->assertSame('new-key', $config->getApiKey());
        $this->assertSame('https://custom.api.com:8181/api/1', $config->getBaseUrl());
        $this->assertSame(60, $config->getTimeout());
        $this->assertSame(20, $config->getConnectTimeout());
        $this->assertFalse($config->getVerifySsl());
        $this->assertSame('/path/to/cacert.pem', $config->getCaBundlePath());
        $this->assertSame('CustomAgent/2.0', $config->getUserAgent());
        $this->assertSame(['X-Custom' => 'HeaderValue', 'X-Second' => 'Value2'], $config->getHeaders());
        $this->assertSame([
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_VERBOSE => true,
        ], $config->getCurlOptions());
    }
}
