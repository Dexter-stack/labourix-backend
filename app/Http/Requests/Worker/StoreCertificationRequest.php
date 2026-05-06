<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isWorker();
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'issuing_body'       => ['nullable', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'issued_at'          => ['nullable', 'date', 'before_or_equal:today'],
            'expires_at'         => ['nullable', 'date', 'after:issued_at'],
            'document'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'document.max'   => 'Document must not exceed 5 MB.',
            'document.mimes' => 'Document must be a PDF, JPG, or PNG file.',
        ];
    }
}
