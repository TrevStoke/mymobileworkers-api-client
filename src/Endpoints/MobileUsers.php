<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class MobileUsers extends AbstractEndpoint
{
    /**
     * Get all mobile users.
     *
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->httpGet('/mobile_users')->toArray();
    }

    /**
     * Alias for all().
     *
     * @return array<mixed>
     */
    public function getAll(): array
    {
        return $this->all();
    }

    /**
     * Create a new mobile user.
     *
     * @param array<string, mixed> $userData
     * @return array<string, mixed>
     */
    public function create(array $userData): array
    {
        return $this->httpPost('/mobile_users', $userData)->toArray();
    }

    /**
     * Get latest vehicle assignment by date and mobile user username.
     *
     * @param string $username
     * @param string $date (format: YYYY-MM-DD)
     * @return array<string, mixed>
     */
    public function getVehicleAssignment(string $username, string $date): array
    {
        return $this->httpGet('/mobile_users/vehicle_assignment', [
            'username' => $username,
            'date' => $date,
        ])->toArray();
    }

    /**
     * Get the current GPS location for a mobile user.
     *
     * @param string $username
     * @return array<string, mixed>
     */
    public function getLocation(string $username): array
    {
        return $this->httpGet('/mobile_users/location', [
            'username' => $username,
        ])->toArray();
    }

    /**
     * Get logs for a mobile user on a given date.
     *
     * @param string $username
     * @param string $date (format: YYYY-MM-DD)
     * @return array<mixed>
     */
    public function getLogs(string $username, string $date): array
    {
        return $this->httpGet('/mobile_users/logs', [
            'username' => $username,
            'date' => $date,
        ])->toArray();
    }
}
