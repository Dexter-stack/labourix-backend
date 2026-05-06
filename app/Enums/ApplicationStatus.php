<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending     = 'pending';
    case Shortlisted = 'shortlisted';
    case Rejected    = 'rejected';
}
