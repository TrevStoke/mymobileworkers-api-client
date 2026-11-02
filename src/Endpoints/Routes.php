<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Routes extends AbstractEndpoint
{
    /**
     * Get all routes.
     *
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->httpGet('/routes')->toArray();
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
     * Get jobs assigned to a route.
     *
     * @param string $routeCode
     * @return array<mixed>
     */
    public function getJobs(string $routeCode): array
    {
        return $this->httpGet('/routes/route/jobs', ['route_code' => $routeCode])->toArray();
    }

    /**
     * Get total weight of a route.
     *
     * @param string $routeCode
     * @return array<string, mixed>
     */
    public function getWeight(string $routeCode): array
    {
        return $this->httpGet('/routes/route/weight', ['route_code' => $routeCode])->toArray();
    }

    /**
     * Get limit of a route.
     *
     * @param string $routeCode
     * @return array<string, mixed>
     */
    public function getLimit(string $routeCode): array
    {
        return $this->httpGet('/routes/route/limit', ['route_code' => $routeCode])->toArray();
    }
}
