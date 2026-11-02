<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Forms extends AbstractEndpoint
{
    /**
     * Get form by form name.
     *
     * @param string $formName
     * @return array<string, mixed>
     */
    public function getByName(string $formName): array
    {
        return $this->httpGet('/forms/form', ['form_name' => $formName])->toArray();
    }

    /**
     * Get form by form name and mobile user username.
     *
     * @param string $formName
     * @param string $username
     * @return array<string, mixed>
     */
    public function getByNameAndUsername(string $formName, string $username): array
    {
        return $this->httpGet('/forms/form/user', [
            'form_name' => $formName,
            'username' => $username,
        ])->toArray();
    }
}
