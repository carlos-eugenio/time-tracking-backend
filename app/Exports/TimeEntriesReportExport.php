<?php

namespace App\Exports;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TimeEntriesReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly Builder $query)
    {
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Funcionário',
            'Data',
            'Início',
            'Fim',
            'Total em minutos',
            'Observações',
        ];
    }

    public function map($row): array
    {
        $startedAt = $row->started_at instanceof CarbonInterface ? $row->started_at : null;
        $endedAt = $row->ended_at instanceof CarbonInterface ? $row->ended_at : null;

        $totalMinutes = null;
        if ($startedAt && $endedAt) {
            $totalMinutes = (int) $startedAt->diffInMinutes($endedAt, true);
        }

        return [
            (string) $row->employee_name,
            $startedAt?->format('d/m/Y'),
            $startedAt?->format('d/m/Y H:i:s'),
            $endedAt?->format('d/m/Y H:i:s'),
            $totalMinutes,
            $row->notes,
        ];
    }
}
