<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $perPage = max(1, min(100, $perPage));

        return Employee::query()
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return Employee::query()->create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->fill($data);
        $employee->save();

        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }

    public function setActive(Employee $employee, bool $isActive): Employee
    {
        $employee->is_active = $isActive;
        $employee->save();

        return $employee;
    }
}

