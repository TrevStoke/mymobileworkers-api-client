<?php

declare(strict_types=1);

namespace MyMobileWorkers\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidArgumentException extends BaseInvalidArgumentException implements MyMobileWorkersException
{
}
