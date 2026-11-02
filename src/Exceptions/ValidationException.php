<?php

declare(strict_types=1);

namespace MyMobileWorkers\Exceptions;

class ValidationException extends ApiException
{
    /**
     * @var array<string, mixed>|array<int, mixed>
     */
    protected array $errors;

    /**
     * @param array<string, mixed>|array<int, mixed> $errors
     * @param mixed $responseBody
     */
    public function __construct(
        string $message = '',
        int $statusCode = 400,
        $responseBody = null,
        ?string $apiErrorCode = null,
        array $errors = []
    ) {
        parent::__construct($message, $statusCode, $responseBody, $apiErrorCode);
        $this->errors = $errors;
    }

    /**
     * @return array<string, mixed>|array<int, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
