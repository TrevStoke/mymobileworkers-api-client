<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class VehicleChecks extends AbstractEndpoint
{
    /**
     * Get vehicle checks by registration plate.
     *
     * @param string $registration
     * @param array<string, mixed> $extraParams
     * @return array<mixed>
     */
    public function getByRegistration(string $registration, array $extraParams = []): array
    {
        $params = array_merge(['registration' => $registration], $extraParams);
        return $this->httpGet('/vehicle_checks/registration', $params)->toArray();
    }
}
