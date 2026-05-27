<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeEntries\StoreTimeEntryRequest;
use App\Http\Requests\TimeEntries\UpdateTimeEntryRequest;
use App\Http\Resources\TimeEntryResource;
use App\Models\TimeEntry;
use App\Services\TimeEntries\TimeEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function __construct(private readonly TimeEntryService $timeEntries)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $employeeId = $request->query('employee_id');
        $employeeId = is_null($employeeId) ? null : (int) $employeeId;

        $paginator = $this->timeEntries->paginate($employeeId, $perPage);

        return response()->json(
            TimeEntryResource::collection($paginator)->response()->getData(true)
        );
    }

    public function create(StoreTimeEntryRequest $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $timeEntry = $this->timeEntries->create($request->validated(), $userId);

        return response()->json([
            'data' => TimeEntryResource::make($timeEntry),
        ], 201);
    }

    public function show(TimeEntry $timeEntry): JsonResponse
    {
        return response()->json([
            'data' => TimeEntryResource::make($timeEntry),
        ]);
    }

    public function update(UpdateTimeEntryRequest $request, TimeEntry $timeEntry): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $timeEntry = $this->timeEntries->update($timeEntry, $request->validated(), $userId);

        return response()->json([
            'data' => TimeEntryResource::make($timeEntry),
        ]);
    }

    public function delete(TimeEntry $timeEntry): JsonResponse
    {
        $this->timeEntries->delete($timeEntry);

        return response()->json([], 204);
    }
}

