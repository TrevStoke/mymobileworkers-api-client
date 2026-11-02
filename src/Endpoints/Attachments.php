<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

use CURLFile;
use MyMobileWorkers\Exceptions\InvalidArgumentException;

class Attachments extends AbstractEndpoint
{
    /**
     * Create an attachment for a job.
     *
     * @param string $jobNumber
     * @param string $filePath
     * @param array<string, mixed> $extraParams
     * @return array<string, mixed>
     */
    public function createJobAttachment(string $jobNumber, string $filePath, array $extraParams = []): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Attachment file not found: {$filePath}");
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fields = array_merge([
            'job_number' => $jobNumber,
            'file' => new CURLFile($filePath, $mimeType, basename($filePath)),
        ], $extraParams);

        return $this->httpPostMultipart('/attachments/job', $fields)->toArray();
    }

    /**
     * Delete a job attachment by attachment ID.
     *
     * @param string $attachmentId
     * @return array<string, mixed>
     */
    public function deleteJobAttachment(string $attachmentId): array
    {
        return $this->httpDelete('/attachments/job', ['attachment_id' => $attachmentId])->toArray();
    }

    /**
     * Create a company attachment.
     *
     * @param string $filePath
     * @param array<string, mixed> $extraParams
     * @return array<string, mixed>
     */
    public function createCompanyAttachment(string $filePath, array $extraParams = []): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Attachment file not found: {$filePath}");
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fields = array_merge([
            'file' => new CURLFile($filePath, $mimeType, basename($filePath)),
        ], $extraParams);

        return $this->httpPostMultipart('/attachments/company', $fields)->toArray();
    }
}
