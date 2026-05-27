<?php

namespace App\Http\Requests\TimeEntries;

use App\Models\TimeEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('employees', 'id')->where('is_active', true),
            ],
            'started_at' => ['sometimes', 'required', 'date'],
            'ended_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('ended_at')) {
                return;
            }

            $timeEntry = $this->route('timeEntry');

            $startedAt = $this->input('started_at') ?? $timeEntry?->started_at;
            if (!$startedAt) {
                return;
            }

            try {
                $endedAt = new \DateTimeImmutable((string) $this->input('ended_at'));
                $startedAtObj = $startedAt instanceof \DateTimeInterface
                    ? \DateTimeImmutable::createFromInterface($startedAt)
                    : new \DateTimeImmutable((string) $startedAt);

                if ($endedAt <= $startedAtObj) {
                    $validator->errors()->add('ended_at', 'The ended_at field must be a date after started_at.');
                }
            } catch (\Exception $e) { 
              
            }
        });
    }
}

