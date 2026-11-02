<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Assets extends AbstractEndpoint
{
    /**
     * Create or update assets.
     *
     * @param array<int, array<string, mixed>> $assets
     * @param bool $validateOnly
     * @param bool $updateExisting
     * @return array<string, mixed>
     */
    public function create(array $assets, bool $validateOnly = false, bool $updateExisting = false): array
    {
        $payload = [
            'validate_only' => $validateOnly ? 'true' : 'false',
            'update_existing' => $updateExisting ? 'true' : 'false',
            'assets' => $assets,
        ];

        return $this->httpPost('/assets', $payload)->toArray();
    }

    /**
     * Get an asset by serial number.
     *
     * @param string $serialNumber
     * @return array<string, mixed>
     */
    public function getBySerialNumber(string $serialNumber): array
    {
        return $this->httpGet('/assets/asset', ['serial_number' => $serialNumber])->toArray();
    }

    /**
     * Get assets updated since a given date/timestamp.
     *
     * @param string $since
     * @return array<mixed>
     */
    public function getUpdatedSince(string $since): array
    {
        return $this->httpGet('/assets/updates/since', ['since' => $since])->toArray();
    }

    /**
     * Get assets assigned to a customer.
     *
     * @param string $customerName
     * @return array<mixed>
     */
    public function getAssignedToCustomer(string $customerName): array
    {
        return $this->httpGet('/assets/customer', ['customer' => $customerName])->toArray();
    }

    /**
     * Get assets by item category.
     *
     * @param string $category
     * @return array<mixed>
     */
    public function getByCategory(string $category): array
    {
        return $this->httpGet('/assets/category', ['category' => $category])->toArray();
    }

    /**
     * Get asset history by serial number.
     *
     * @param string $serialNumber
     * @return array<mixed>
     */
    public function getHistory(string $serialNumber): array
    {
        return $this->httpGet('/assets/history', ['serial_number' => $serialNumber])->toArray();
    }

    /**
     * Get all asset statuses.
     *
     * @return array<mixed>
     */
    public function getStatuses(): array
    {
        return $this->httpGet('/assets/statuses')->toArray();
    }
}
