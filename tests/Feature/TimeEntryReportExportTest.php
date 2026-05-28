<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TimeEntryReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_csv_xlsx_and_pdf(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $employee = Employee::factory()->create(['is_active' => true, 'name' => 'Ana']);
        TimeEntry::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => '2026-05-10 08:00:00',
            'ended_at' => '2026-05-10 09:00:00',
            'notes' => 'ok',
        ]);

        $base = '/api/reports/time-entries/export?start_date=2026-05-01&end_date=2026-05-31';

        $this->get($base.'&format=csv')
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->get($base.'&format=xlsx')
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->get($base.'&format=pdf')
            ->assertOk()
            ->assertHeader('content-disposition');
    }
}

