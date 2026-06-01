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

        $paginator = $this->timeEntries->paginate((int) $request->user()->id, $employeeId, $perPage);

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

    public function show(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        $this->ensureTimeEntryBelongsToUser($timeEntry, (int) $request->user()->id);

        return response()->json([
            'data' => TimeEntryResource::make($timeEntry),
        ]);
    }

    public function update(UpdateTimeEntryRequest $request, TimeEntry $timeEntry): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $this->ensureTimeEntryBelongsToUser($timeEntry, $userId);

        $timeEntry = $this->timeEntries->update($timeEntry, $request->validated(), $userId);

        return response()->json([
            'data' => TimeEntryResource::make($timeEntry),
        ]);
    }

    public function delete(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        $this->ensureTimeEntryBelongsToUser($timeEntry, (int) $request->user()->id);

        $this->timeEntries->delete($timeEntry);

        return response()->json([], 204);
    }

    private function ensureTimeEntryBelongsToUser(TimeEntry $timeEntry, int $userId): void
    {
        abort_unless((int) $timeEntry->employee()->value('user_id') === $userId, 404);
    }
}
