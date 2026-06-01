<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\StoreEmployeeRequest;
use App\Http\Requests\Employees\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\Employees\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employees)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $paginator = $this->employees->paginate((int) $request->user()->id, $perPage);

        return response()->json(
            EmployeeResource::collection($paginator)->response()->getData(true)
        );
    }

    public function create(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->employees->create($request->validated(), (int) $request->user()->id);

        return response()->json([
            'data' => EmployeeResource::make($employee),
        ], 201);
    }

    public function show(Request $request, Employee $employee): JsonResponse
    {
        $this->ensureEmployeeBelongsToUser($employee, (int) $request->user()->id);

        return response()->json([
            'data' => EmployeeResource::make($employee),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $this->ensureEmployeeBelongsToUser($employee, (int) $request->user()->id);

        $employee = $this->employees->update($employee, $request->validated());

        return response()->json([
            'data' => EmployeeResource::make($employee),
        ]);
    }

    public function delete(Request $request, Employee $employee): JsonResponse
    {
        $this->ensureEmployeeBelongsToUser($employee, (int) $request->user()->id);

        $this->employees->delete($employee);

        return response()->json([], 204);
    }

    public function activate(Request $request, Employee $employee): JsonResponse
    {
        $this->ensureEmployeeBelongsToUser($employee, (int) $request->user()->id);

        $employee = $this->employees->setActive($employee, true);

        return response()->json([
            'data' => EmployeeResource::make($employee),
        ]);
    }

    public function deactivate(Request $request, Employee $employee): JsonResponse
    {
        $this->ensureEmployeeBelongsToUser($employee, (int) $request->user()->id);

        $employee = $this->employees->setActive($employee, false);

        return response()->json([
            'data' => EmployeeResource::make($employee),
        ]);
    }

    private function ensureEmployeeBelongsToUser(Employee $employee, int $userId): void
    {
        abort_unless((int) $employee->user_id === $userId, 404);
    }
}
