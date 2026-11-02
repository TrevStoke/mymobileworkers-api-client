<?php

declare(strict_types=1);

namespace MyMobileWorkers\Endpoints;

class Companies extends AbstractEndpoint
{
    /**
     * Get company details.
     *
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->httpGet('/companies/company')->toArray();
    }

    /**
     * Create company attachment folders.
     *
     * @param array<int, array<string, mixed>>|array<string, mixed> $folders
     * @return array<string, mixed>
     */
    public function createAttachmentFolders(array $folders): array
    {
        return $this->httpPost('/companies/attachment_folders', [
            'attachment_folders' => $folders,
        ])->toArray();
    }
}
