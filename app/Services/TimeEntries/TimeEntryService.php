<?php

namespace App\Services\TimeEntries;

use App\Models\TimeEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TimeEntryService
{
    public function paginate(int $userId, ?int $employeeId, int $perPage = 15): LengthAwarePaginator
    {
        $perPage = max(1, min(100, $perPage));

        return TimeEntry::query()
            ->whereHas('employee', fn ($query) => $query->where('user_id', $userId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->orderByDesc('started_at')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): TimeEntry
    {
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        return TimeEntry::query()->create($data);
    }

    public function update(TimeEntry $timeEntry, array $data, int $userId): TimeEntry
    {
        $data['updated_by'] = $userId;

        $timeEntry->fill($data);
        $timeEntry->save();

        return $timeEntry;
    }

    public function delete(TimeEntry $timeEntry): void
    {
        $timeEntry->delete();
    }
}
