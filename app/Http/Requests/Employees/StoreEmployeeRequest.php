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
        $userId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')->where(fn ($query) => $query->where('user_id', $userId))],
            'document' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'document')->where(fn ($query) => $query->where('user_id', $userId))],
            'job_title' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'registration_number')->where(fn ($query) => $query->where('user_id', $userId))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
