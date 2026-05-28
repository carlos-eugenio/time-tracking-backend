<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TimeEntryReportExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')],
            'sort' => ['sometimes', Rule::in(['name', 'date'])],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'format' => ['required', Rule::in(['csv', 'xlsx', 'pdf'])],
        ];
    }
}

