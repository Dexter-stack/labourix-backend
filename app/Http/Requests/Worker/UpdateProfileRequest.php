<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isWorker();
    }

    public function rules(): array
    {
        return [
            'trade'                  => ['sometimes', 'string', 'max:100'],
            'skills'                 => ['sometimes', 'array'],
            'skills.*'               => ['string'],
            'location'               => ['sometimes', 'string', 'max:255'],
            'latitude'               => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'              => ['nullable', 'numeric', 'between:-180,180'],
            'hourly_rate'            => ['sometimes', 'numeric', 'min:0'],
            'bio'                    => ['nullable', 'string', 'max:1000'],
            'availability_schedule'  => ['sometimes', 'array'],
        ];
    }
}
