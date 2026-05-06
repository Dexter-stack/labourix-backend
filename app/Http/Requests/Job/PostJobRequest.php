<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class PostJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isEmployer();
    }

    public function rules(): array
    {
        return [
            'title'                    => ['required', 'string', 'max:255'],
            'description'              => ['required', 'string'],
            'trade'                    => ['required', 'string', 'max:100'],
            'required_skills'          => ['required', 'array'],
            'required_skills.*'        => ['string'],
            'required_certifications'  => ['sometimes', 'array'],
            'required_certifications.*' => ['string'],
            'location'                 => ['required', 'string', 'max:255'],
            'latitude'                 => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'                => ['nullable', 'numeric', 'between:-180,180'],
            'hourly_rate'              => ['required', 'numeric', 'min:0'],
            'start_date'               => ['required', 'date', 'after:now'],
            'end_date'                 => ['nullable', 'date', 'after:start_date'],
            'workers_needed'           => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
