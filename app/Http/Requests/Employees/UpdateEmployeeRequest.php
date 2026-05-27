<?php

namespace App\Http\Requests\Employees;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Employee|null $employee */
        $employee = $this->route('employee');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee?->id)],
            'document' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'document')->ignore($employee?->id)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'registration_number')->ignore($employee?->id)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

