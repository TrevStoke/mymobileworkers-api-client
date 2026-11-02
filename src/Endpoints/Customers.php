<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Customers extends AbstractEndpoint
{
    /**
     * Add a new customer.
     *
     * @param array<string, mixed> $customerData
     * @return array<string, mixed>
     */
    public function create(array $customerData): array
    {
        return $this->httpPost('/customers', $customerData)->toArray();
    }

    /**
     * Update customer name.
     *
     * @param string $currentName
     * @param string $newName
     * @return array<string, mixed>
     */
    public function updateName(string $currentName, string $newName): array
    {
        return $this->httpPost('/customers/customer/name', [
            'current_name' => $currentName,
            'new_name' => $newName,
        ])->toArray();
    }

    /**
     * Create customer sites.
     *
     * @param array<int, array<string, mixed>> $sites
     * @param bool $validateOnly
     * @param bool $updateExisting
     * @return array<string, mixed>
     */
    public function createSites(array $sites, bool $validateOnly = false, bool $updateExisting = false): array
    {
        $payload = [
            'validate_only' => $validateOnly ? 'true' : 'false',
            'update_existing' => $updateExisting ? 'true' : 'false',
            'customer_sites' => $sites,
        ];

        return $this->httpPost('/customers/customer/sites', $payload)->toArray();
    }

    /**
     * Create customer contacts/sites.
     *
     * @param array<int, array<string, mixed>> $contacts
     * @param bool $validateOnly
     * @param bool $updateExisting
     * @return array<string, mixed>
     */
    public function createContacts(array $contacts, bool $validateOnly = false, bool $updateExisting = false): array
    {
        $payload = [
            'validate_only' => $validateOnly ? 'true' : 'false',
            'update_existing' => $updateExisting ? 'true' : 'false',
            'customer_contacts' => $contacts,
        ];

        return $this->httpPost('/customers/customer/contacts', $payload)->toArray();
    }
}
