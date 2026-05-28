<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\TimeEntryReportRequest;
use App\Http\Resources\Reports\TimeEntryReportResource;
use App\Services\Reports\TimeEntryReportService;
use Illuminate\Http\JsonResponse;

class TimeEntryReportController extends Controller
{
    public function __construct(private readonly TimeEntryReportService $reports)
    {
    }

    public function index(TimeEntryReportRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $paginator = $this->reports->paginate($filters);
        $totalMinutes = $this->reports->totalMinutes($filters);

        return response()->json([
            'data' => TimeEntryReportResource::collection($paginator)->response()->getData(true)['data'],
            'meta' => array_merge(
                $paginator->toArray()['meta'] ?? [],
                [
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalMinutes / 60, 2),
                ]
            ),
        ]);
    }
}
