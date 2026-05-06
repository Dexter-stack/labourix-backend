<?php

namespace App\Exceptions;

use Exception;

class ComplianceBlockException extends Exception
{
    public function __construct(string $reason = 'Booking blocked due to compliance failure.')
    {
        parent::__construct($reason, 422);
    }
}
