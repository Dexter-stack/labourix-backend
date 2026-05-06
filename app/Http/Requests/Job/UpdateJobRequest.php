<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isEmployer();
    }

    public function rules(): array
    {
        return [
            'title'                    => ['sometimes', 'string', 'max:255'],
            'description'              => ['sometimes', 'string'],
            'trade'                    => ['sometimes', 'string', 'max:100'],
            'required_skills'          => ['sometimes', 'array'],
            'required_skills.*'        => ['string'],
            'required_certifications'  => ['sometimes', 'array'],
            'required_certifications.*' => ['string'],
            'location'                 => ['sometimes', 'string', 'max:255'],
            'hourly_rate'              => ['sometimes', 'numeric', 'min:0'],
            'start_date'               => ['sometimes', 'date'],
            'end_date'                 => ['sometimes', 'date', 'after:start_date'],
            'workers_needed'           => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
