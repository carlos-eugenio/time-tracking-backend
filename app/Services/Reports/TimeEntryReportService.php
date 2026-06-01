<?php

namespace App\Services\Reports;

use App\Models\TimeEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TimeEntryReportService
{
    public function query(array $filters, int $userId): Builder
    {
        $sort = $filters['sort'] ?? 'date';
        $direction = $filters['direction'] ?? 'asc';

        $start = $filters['start_date'].' 00:00:00';
        $end = $filters['end_date'].' 23:59:59';

        $base = TimeEntry::query()
            ->join('employees', 'employees.id', '=', 'time_entries.employee_id')
            ->where('employees.user_id', $userId)
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

        return $base;
    }

    public function paginate(array $filters, int $userId): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = max(1, min(100, $perPage));

        return $this->query($filters, $userId)->paginate($perPage);
    }

    public function totalMinutes(array $filters, int $userId): int
    {
        $start = $filters['start_date'].' 00:00:00';
        $end = $filters['end_date'].' 23:59:59';

        return (int) TimeEntry::query()
            ->join('employees', 'employees.id', '=', 'time_entries.employee_id')
            ->where('employees.user_id', $userId)
            ->whereBetween('time_entries.started_at', [$start, $end])
            ->when(!empty($filters['employee_id']), fn ($q) => $q->where('time_entries.employee_id', (int) $filters['employee_id']))
            ->whereNotNull('time_entries.ended_at')
            ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, time_entries.started_at, time_entries.ended_at)), 0) as total_minutes')
            ->value('total_minutes');
    }
}
