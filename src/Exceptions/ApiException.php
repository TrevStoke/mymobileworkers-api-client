<?php

declare(strict_types=1);

namespace MyMobileWorkers\Exceptions;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException implements MyMobileWorkersException
{
    protected int $statusCode;
    protected $responseBody;
    protected ?string $apiErrorCode;

    public function __construct(
        string $message = '',
        int $statusCode = 0,
        $responseBody = null,
        ?string $apiErrorCode = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        $this->apiErrorCode = $apiErrorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }
}
