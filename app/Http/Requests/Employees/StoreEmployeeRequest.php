<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')],
            'document' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'document')],
            'job_title' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'registration_number')],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

