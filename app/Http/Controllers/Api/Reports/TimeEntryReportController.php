<?php

namespace App\Http\Controllers\Api\Reports;

use App\Exports\TimeEntriesReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\TimeEntryReportExportRequest;
use App\Http\Requests\Reports\TimeEntryReportRequest;
use App\Http\Resources\Reports\TimeEntryReportResource;
use App\Services\Reports\TimeEntryReportService;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'total_minutes' => $totalMinutes,
                'total_hours' => round($totalMinutes / 60, 2),
            ],
        ]);
    }

    public function export(TimeEntryReportExportRequest $request)
    {
        $filters = $request->validated();
        $format = $filters['format'];

        $baseName = 'time-entries-'.$filters['start_date'].'_to_'.$filters['end_date'];
        if (!empty($filters['employee_id'])) {
            $baseName .= '_employee-'.$filters['employee_id'];
        }

        if ($format === 'pdf') {
            $rows = $this->reports->query($filters)->get();
            $totalMinutes = $this->reports->totalMinutes($filters);

            $pdf = Pdf::loadView('reports.time_entries', [
                'rows' => $rows,
                'filters' => $filters,
                'totalMinutes' => $totalMinutes,
            ]);

            return $pdf->download($baseName.'.pdf');
        }

        $query = $this->reports->query($filters);
        $export = new TimeEntriesReportExport($query);

        if ($format === 'csv') {
            return Excel::download($export, $baseName.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download($export, $baseName.'.xlsx');
    }
}
