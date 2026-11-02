<?php

declare(strict_types=1);

namespace MyMobileWorkers\Exceptions;

use RuntimeException;
use Throwable;

class NetworkException extends RuntimeException implements MyMobileWorkersException
{
    protected int $curlErrorCode;

    public function __construct(string $message = '', int $curlErrorCode = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $curlErrorCode, $previous);
        $this->curlErrorCode = $curlErrorCode;
    }

    public function getCurlErrorCode(): int
    {
        return $this->curlErrorCode;
    }
}
