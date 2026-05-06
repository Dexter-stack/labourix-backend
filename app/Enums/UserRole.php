<?php

namespace App\Enums;

enum UserRole: string
{
    case Employer = 'employer';
    case Worker = 'worker';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
}
