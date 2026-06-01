<?php

namespace App\Http\Requests\TimeEntries;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')
                    ->where('is_active', true)
                    ->where('user_id', $userId),
            ],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after:started_at'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
