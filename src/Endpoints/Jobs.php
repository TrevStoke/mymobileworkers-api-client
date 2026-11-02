<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

use CURLFile;
use MyMobileWorkers\Exceptions\InvalidArgumentException;

class Jobs extends AbstractEndpoint
{
    /**
     * Get a specific job by job number.
     *
     * @param string $jobNumber
     * @return array<string, mixed>
     */
    public function get(string $jobNumber): array
    {
        return $this->httpGet('/jobs/job', ['job_number' => $jobNumber])->toArray();
    }

    /**
     * Get jobs by current status.
     *
     * @param string $status
     * @param string|null $startDate (format: YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
     * @param string|null $endDate
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getByCurrentStatus(
        string $status,
        ?string $startDate = null,
        ?string $endDate = null,
        array $extraParams = []
    ): array {
        $params = array_merge(['status' => $status], $extraParams);
        if ($startDate !== null) {
            $params['start_date'] = $startDate;
        }
        if ($endDate !== null) {
            $params['end_date'] = $endDate;
        }

        return $this->httpGet('/jobs/current_status', $params)->toArray();
    }

    /**
     * Get jobs by activity status.
     *
     * @param string $status
     * @param string|null $startDate
     * @param string|null $endDate
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getByActivityStatus(
        string $status,
        ?string $startDate = null,
        ?string $endDate = null,
        array $extraParams = []
    ): array {
        $params = array_merge(['status' => $status], $extraParams);
        if ($startDate !== null) {
            $params['start_date'] = $startDate;
        }
        if ($endDate !== null) {
            $params['end_date'] = $endDate;
        }

        return $this->httpGet('/jobs/activity_status', $params)->toArray();
    }

    /**
     * Get jobs updated since a given date/timestamp.
     *
     * @param string $since (format: YYYY-MM-DD HH:MM:SS)
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getUpdatesSince(string $since, array $extraParams = []): array
    {
        $params = array_merge(['since' => $since], $extraParams);
        return $this->httpGet('/jobs/updates/since', $params)->toArray();
    }

    /**
     * Get jobs updated between two dates/timestamps.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getUpdatesBetween(string $startDate, string $endDate, array $extraParams = []): array
    {
        $params = array_merge([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], $extraParams);

        return $this->httpGet('/jobs/updates/between', $params)->toArray();
    }

    /**
     * Get photos added since a given date/timestamp.
     *
     * @param string $since
     * @return array<mixed>
     */
    public function getPhotosAddedSince(string $since): array
    {
        return $this->httpGet('/jobs/photos/since', ['since' => $since])->toArray();
    }

    /**
     * Create or update one or multiple jobs.
     *
     * @param array<int, array<string, mixed>> $jobs
     * @param bool $validateOnly
     * @param bool $updateExisting
     * @return array<string, mixed>
     */
    public function create(array $jobs, bool $validateOnly = false, bool $updateExisting = false): array
    {
        $payload = [
            'validate_only' => $validateOnly ? 'true' : 'false',
            'update_existing' => $updateExisting ? 'true' : 'false',
            'jobs' => $jobs,
        ];

        return $this->httpPost('/jobs', $payload)->toArray();
    }

    /**
     * Create jobs using multipart form-data (JSON file upload).
     *
     * @param string $jsonFilePath
     * @param bool $validateOnly
     * @param bool $updateExisting
     * @return array<string, mixed>
     */
    public function createFromFile(string $jsonFilePath, bool $validateOnly = false, bool $updateExisting = false): array
    {
        if (!file_exists($jsonFilePath)) {
            throw new InvalidArgumentException("File not found: {$jsonFilePath}");
        }

        $fields = [
            'file' => new CURLFile($jsonFilePath, 'application/json', basename($jsonFilePath)),
            'validate_only' => $validateOnly ? 'true' : 'false',
            'update_existing' => $updateExisting ? 'true' : 'false',
        ];

        return $this->httpPostMultipart('/jobs', $fields)->toArray();
    }

    /**
     * Delete a single job by job number.
     *
     * @param string $jobNumber
     * @return array<string, mixed>
     */
    public function delete(string $jobNumber): array
    {
        return $this->httpPost('/jobs/delete', ['job_number' => $jobNumber])->toArray();
    }

    /**
     * Delete multiple jobs by job numbers.
     *
     * @param array<string> $jobNumbers
     * @return array<string, mixed>
     */
    public function deleteMultiple(array $jobNumbers): array
    {
        return $this->httpPost('/jobs/delete_jobs', ['jobs' => $jobNumbers])->toArray();
    }

    /**
     * Create job items for a specific job.
     *
     * @param string $jobNumber
     * @param array<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    public function createItems(string $jobNumber, array $items): array
    {
        return $this->httpPost('/jobs/job/items', [
            'job_number' => $jobNumber,
            'items' => $items,
        ])->toArray();
    }

    /**
     * Delete job items from a specific job.
     *
     * @param string $jobNumber
     * @param array<int, array<string, mixed>>|array<string> $items
     * @return array<string, mixed>
     */
    public function deleteItems(string $jobNumber, array $items): array
    {
        return $this->httpDelete('/jobs/job/items', [
            'job_number' => $jobNumber,
            'items' => $items,
        ])->toArray();
    }

    /**
     * Create job parts for a specific job.
     *
     * @param string $jobNumber
     * @param array<int, array<string, mixed>> $parts
     * @return array<string, mixed>
     */
    public function createParts(string $jobNumber, array $parts): array
    {
        return $this->httpPost('/jobs/job/parts', [
            'job_number' => $jobNumber,
            'parts' => $parts,
        ])->toArray();
    }

    /**
     * Get the PDF for a job (returns raw binary/string content).
     *
     * @param string $jobNumber
     * @return string
     */
    public function getPdf(string $jobNumber): string
    {
        return $this->httpGet('/jobs/job/pdf', ['job_number' => $jobNumber])->getRawBody();
    }

    /**
     * Email the job PDF.
     *
     * @param string $jobNumber
     * @param string $email
     * @param array<string, mixed> $extraParams
     * @return array<string, mixed>
     */
    public function emailPdf(string $jobNumber, string $email, array $extraParams = []): array
    {
        $payload = array_merge([
            'job_number' => $jobNumber,
            'email' => $email,
        ], $extraParams);

        return $this->httpPost('/jobs/job/pdf/email', $payload)->toArray();
    }

    /**
     * Set the status of a job.
     *
     * @param string $jobNumber
     * @param string $status
     * @param array<string, mixed> $extraParams
     * @return array<string, mixed>
     */
    public function setStatus(string $jobNumber, string $status, array $extraParams = []): array
    {
        $payload = array_merge([
            'job_number' => $jobNumber,
            'status' => $status,
        ], $extraParams);

        return $this->httpPost('/jobs/job/status', $payload)->toArray();
    }

    /**
     * Link two jobs together.
     *
     * @param string $parentJobNumber
     * @param string $childJobNumber
     * @return array<string, mixed>
     */
    public function link(string $parentJobNumber, string $childJobNumber): array
    {
        return $this->httpPost('/jobs/job/link', [
            'parent_job_number' => $parentJobNumber,
            'child_job_number' => $childJobNumber,
        ])->toArray();
    }

    /**
     * Update PO number and Invoice number for a job.
     *
     * @param string $jobNumber
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateInvoicing(string $jobNumber, array $data): array
    {
        $params = ['job_number' => $jobNumber];
        return $this->httpPost('/jobs/job/status/invoicing', $data, $params)->toArray();
    }

    /**
     * Create or update a job tracking reference.
     *
     * @param string $jobNumber
     * @param string $trackingReference
     * @return array<string, mixed>
     */
    public function createTrackingReference(string $jobNumber, string $trackingReference): array
    {
        return $this->httpPost('/jobs/job/tracking', [
            'job_number' => $jobNumber,
            'tracking_reference' => $trackingReference,
        ])->toArray();
    }
}
