<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Invoices extends AbstractEndpoint
{
    /**
     * Get invoices updated since a given date/timestamp.
     *
     * @param string $since (format: YYYY-MM-DD HH:MM:SS)
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getUpdatedSince(string $since, array $extraParams = []): array
    {
        $params = array_merge(['since' => $since], $extraParams);
        return $this->httpGet('/invoices/updates/since', $params)->toArray();
    }

    /**
     * Get invoices updated between two dates/timestamps.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getUpdatedBetween(string $startDate, string $endDate, array $extraParams = []): array
    {
        $params = array_merge([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], $extraParams);

        return $this->httpGet('/invoices/updates/between', $params)->toArray();
    }

    /**
     * Get invoices created since a given date/timestamp.
     *
     * @param string $since
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getCreatedSince(string $since, array $extraParams = []): array
    {
        $params = array_merge(['since' => $since], $extraParams);
        return $this->httpGet('/invoices/created/since', $params)->toArray();
    }

    /**
     * Get invoices created between two dates/timestamps.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getCreatedBetween(string $startDate, string $endDate, array $extraParams = []): array
    {
        $params = array_merge([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], $extraParams);

        return $this->httpGet('/invoices/created/between', $params)->toArray();
    }
}
