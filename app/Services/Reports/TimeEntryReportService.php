<?php

namespace App\Services\Reports;

use App\Models\TimeEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TimeEntryReportService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? 'date';
        $direction = $filters['direction'] ?? 'asc';
        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = max(1, min(100, $perPage));

        $start = $filters['start_date'].' 00:00:00';
        $end = $filters['end_date'].' 23:59:59';

        $base = TimeEntry::query()
            ->join('employees', 'employees.id', '=', 'time_entries.employee_id')
            ->whereBetween('time_entries.started_at', [$start, $end])
            ->when(!empty($filters['employee_id']), fn ($q) => $q->where('time_entries.employee_id', (int) $filters['employee_id']))
            ->select([
                'time_entries.*',
                'employees.name as employee_name',
            ]);

        if ($sort === 'name') {
            $base->orderBy('employees.name', $direction);
            $base->orderBy('time_entries.started_at', 'asc');
        } else {
            $base->orderBy('time_entries.started_at', $direction);
            $base->orderBy('employees.name', 'asc');
        }

        return $base->paginate($perPage);
    }

    public function totalMinutes(array $filters): int
    {
        $start = $filters['start_date'].' 00:00:00';
        $end = $filters['end_date'].' 23:59:59';

        return (int) TimeEntry::query()
            ->whereBetween('started_at', [$start, $end])
            ->when(!empty($filters['employee_id']), fn ($q) => $q->where('employee_id', (int) $filters['employee_id']))
            ->whereNotNull('ended_at')
            ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, started_at, ended_at)), 0) as total_minutes')
            ->value('total_minutes');
    }
}
