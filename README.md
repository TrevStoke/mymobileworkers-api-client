# MyMobileWorkers API Client for PHP

A modern, object-oriented PHP client for the [MyMobileWorkers API](https://docs.mymobileworkers.com/index.php?title=Main_Page) using cURL for HTTP transport.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

## Features

- **Full API Coverage**: Endpoints for Jobs, Assets, Mobile Users, Customers, Routes, Attachments, Invoices, Forms, Vehicle Checks, and Companies.
- **cURL Transport**: Fast and reliable HTTP communication using native PHP cURL extension with zero bloated external HTTP dependencies.
- **Robust Error Handling**: Dedicated exception hierarchy (`AuthenticationException`, `ValidationException`, `NotFoundException`, `NetworkException`, `ApiException`).
- **Flexible Configuration**: Configurable API key, base URL, timeouts, SSL verification, custom CA bundles, and cURL options.
- **Extensible & Testable**: Built with interface-driven design (`HttpClientInterface`), fully unit-tested with PHPUnit.

---

## Requirements

- PHP >= 7.4 or PHP 8.x (including PHP 8.1, 8.2, 8.3, 8.4, 8.5)
- `ext-curl`
- `ext-json`

---

## Installation

Install the package via [Composer](https://getcomposer.org/):

```bash
composer require mymobileworkers/api-client
```

---

## Quickstart

```php
<?php

require_once 'vendor/autoload.php';

use MyMobileWorkers\Client;

// Initialize client with your API key
$client = new Client('YOUR_MYMOBILEWORKERS_API_KEY');

// 1. Get a job by job number
$job = $client->jobs()->get('JOB-100234');
print_r($job);

// 2. Fetch all mobile users
$users = $client->mobileUsers()->all();
print_r($users);

// 3. Create or update jobs
$response = $client->jobs()->create([
    [
        'job_number' => 'JOB-100235',
        'address_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
        'contact_name' => 'John Smith',
        'contact_number_1' => '07700900000',
        'type' => 'repair',
        'special_instructions' => 'Ring doorbell on arrival',
    ]
], validateOnly: false, updateExisting: true);

print_r($response);
```

---

## Advanced Configuration

You can customize the client settings using the `Config` class:

```php
use MyMobileWorkers\Client;
use MyMobileWorkers\Config;

$config = new Config(
    apiKey: 'YOUR_API_KEY',
    baseUrl: 'https://api.mymobileworkers.com:8181/api/1', // Default API endpoint
    timeout: 45, // Request timeout in seconds
    connectTimeout: 10, // Connection timeout in seconds
    verifySsl: true, // SSL certificate verification
    caBundlePath: '/path/to/cacert.pem' // Optional custom CA bundle
);

$client = new Client($config);
```

---

## API Endpoints Reference

### 1. Jobs (`$client->jobs()`)

```php
// Get single job details
$job = $client->jobs()->get('JOB-001');

// Get jobs by current status
$newJobs = $client->jobs()->getByCurrentStatus('NEW', '2026-01-01', '2026-01-31');

// Get jobs by activity status
$inProgress = $client->jobs()->getByActivityStatus('IN_PROGRESS');

// Get updates since / between dates
$recentUpdates = $client->jobs()->getUpdatesSince('2026-01-01 00:00:00');
$rangeUpdates = $client->jobs()->getUpdatesBetween('2026-01-01', '2026-01-15');

// Get photos added since timestamp
$photos = $client->jobs()->getPhotosAddedSince('2026-01-01 00:00:00');

// Create jobs from array payload
$result = $client->jobs()->create($jobsArray, validateOnly: false, updateExisting: true);

// Create jobs from JSON file upload (multipart)
$result = $client->jobs()->createFromFile('/path/to/jobs.json', validateOnly: false);

// Delete job(s)
$client->jobs()->delete('JOB-001');
$client->jobs()->deleteMultiple(['JOB-001', 'JOB-002']);

// Job items and parts
$client->jobs()->createItems('JOB-001', [['name' => 'Valve', 'quantity' => 1]]);
$client->jobs()->deleteItems('JOB-001', ['item-id-1']);
$client->jobs()->createParts('JOB-001', [['part_number' => 'PN-456', 'quantity' => 2]]);

// Job PDF
$pdfBinary = $client->jobs()->getPdf('JOB-001');
$client->jobs()->emailPdf('JOB-001', 'client@example.com');

// Set status & linking
$client->jobs()->setStatus('JOB-001', 'COMPLETE');
$client->jobs()->link('PARENT_JOB_1', 'CHILD_JOB_2');

// Invoicing & tracking references
$client->jobs()->updateInvoicing('JOB-001', ['po_number' => 'PO-1234', 'invoice_number' => 'INV-5678']);
$client->jobs()->createTrackingReference('JOB-001', 'TRACK-999');
```

### 2. Assets (`$client->assets()`)

```php
// Create or update assets
$client->assets()->create([
    ['serial_number' => 'SN-1001', 'name' => 'Generator 5kW']
]);

// Query assets
$asset = $client->assets()->getBySerialNumber('SN-1001');
$updates = $client->assets()->getUpdatedSince('2026-01-01');
$customerAssets = $client->assets()->getAssignedToCustomer('Acme Ltd');
$categoryAssets = $client->assets()->getByCategory('Machinery');
$history = $client->assets()->getHistory('SN-1001');
$statuses = $client->assets()->getStatuses();
```

### 3. Mobile Users (`$client->mobileUsers()`)

```php
// List all mobile users
$users = $client->mobileUsers()->all();

// Create mobile user
$client->mobileUsers()->create([
    'username' => 'johndoe',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'password' => 'SecurePass123',
]);

// GPS location, vehicle assignment and logs
$location = $client->mobileUsers()->getLocation('johndoe');
$vehicle = $client->mobileUsers()->getVehicleAssignment('johndoe', '2026-01-15');
$logs = $client->mobileUsers()->getLogs('johndoe', '2026-01-15');
```

### 4. Customers (`$client->customers()`)

```php
// Add customer
$client->customers()->create(['name' => 'British Gas']);

// Update customer name
$client->customers()->updateName('Old Name', 'New Name');

// Create customer sites & contacts
$client->customers()->createSites([['name' => 'Depot 1', 'address_1' => 'Station Road']]);
$client->customers()->createContacts([['contact_name' => 'Jane Doe', 'address_1' => 'Unit 3']]);
```

### 5. Routes (`$client->routes()`)

```php
// Get all routes
$routes = $client->routes()->all();

// Route jobs, weight and limit
$jobs = $client->routes()->getJobs('ROUTE_NORTH');
$weight = $client->routes()->getWeight('ROUTE_NORTH');
$limit = $client->routes()->getLimit('ROUTE_NORTH');
```

### 6. Attachments (`$client->attachments()`)

```php
// Upload job attachment
$client->attachments()->createJobAttachment('JOB-001', '/path/to/site-photo.jpg', [
    'description' => 'Site before work began'
]);

// Delete attachment
$client->attachments()->deleteJobAttachment('ATT-987');

// Upload company attachment
$client->attachments()->createCompanyAttachment('/path/to/insurance.pdf', [
    'folder' => 'Compliance'
]);
```

### 7. Invoices (`$client->invoices()`)

```php
$updatedSince = $client->invoices()->getUpdatedSince('2026-01-01');
$updatedBetween = $client->invoices()->getUpdatedBetween('2026-01-01', '2026-01-31');
$createdSince = $client->invoices()->getCreatedSince('2026-01-01');
$createdBetween = $client->invoices()->getCreatedBetween('2026-01-01', '2026-01-31');
```

### 8. Forms (`$client->forms()`)

```php
$form = $client->forms()->getByName('Vehicle Safety Inspection');
$userForm = $client->forms()->getByNameAndUsername('Vehicle Safety Inspection', 'johndoe');
```

### 9. Vehicle Checks (`$client->vehicleChecks()`)

```php
$checks = $client->vehicleChecks()->getByRegistration('AB12 CDE');
```

### 10. Companies (`$client->companies()`)

```php
$company = $client->companies()->get();
$client->companies()->createAttachmentFolders([['name' => 'RAMS Documents']]);
```

---

## Error Handling

All client exceptions implement `MyMobileWorkers\Exceptions\MyMobileWorkersException`:

```php
use MyMobileWorkers\Exceptions\AuthenticationException;
use MyMobileWorkers\Exceptions\ValidationException;
use MyMobileWorkers\Exceptions\NotFoundException;
use MyMobileWorkers\Exceptions\NetworkException;
use MyMobileWorkers\Exceptions\ApiException;

try {
    $client->jobs()->get('NON_EXISTENT_JOB');
} catch (AuthenticationException $e) {
    // 401 / 403: Invalid API key or unauthorized
    echo "Auth error: " . $e->getMessage();
} catch (NotFoundException $e) {
    // 404: Resource not found
    echo "Not found: " . $e->getMessage();
} catch (ValidationException $e) {
    // 400 / 422: Validation failure
    echo "Validation error: " . $e->getMessage();
    print_r($e->getErrors());
} catch (NetworkException $e) {
    // Network / cURL timeout or connection issue
    echo "Network error (" . $e->getCurlErrorCode() . "): " . $e->getMessage();
} catch (ApiException $e) {
    // Other API errors (e.g. 500)
    echo "API error [" . $e->getStatusCode() . "]: " . $e->getMessage();
}
```

---

## Testing

Run PHPUnit tests:

```bash
composer test
```

Or run directly:

```bash
./vendor/bin/phpunit
```

---

## License

This project is open-sourced under the [MIT License](LICENSE).
