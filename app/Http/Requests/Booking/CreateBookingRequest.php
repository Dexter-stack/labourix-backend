<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isEmployer();
    }

    public function rules(): array
    {
        return [
            'job_listing_id' => ['required', 'integer', 'exists:job_listings,id'],
            'worker_id'      => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
