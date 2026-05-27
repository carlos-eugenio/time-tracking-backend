<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeEntryReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $startedAt = $this->started_at?->toISOString();
        $endedAt = $this->ended_at?->toISOString();

        $durationMinutes = null;
        if ($this->ended_at) {
            $durationMinutes = (int) $this->ended_at->diffInMinutes($this->started_at);
        }

        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee_name,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
        ];
    }
}

