<?php

namespace App\Events;

use App\Models\Certification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CertificationExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Certification $certification) {}
}
