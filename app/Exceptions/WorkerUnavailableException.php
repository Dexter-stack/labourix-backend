<?php

namespace App\Exceptions;

use Exception;

class WorkerUnavailableException extends Exception
{
    public function __construct(string $reason = 'Worker is not available for the requested period.')
    {
        parent::__construct($reason, 409);
    }
}
