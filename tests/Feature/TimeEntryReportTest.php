<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TimeEntryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_report(): void
    {
        $this->getJson('/api/reports/time-entries?start_date=2026-05-01&end_date=2026-05-31')
            ->assertUnauthorized();
    }

    public function test_report_filters_by_period_and_sums_total_minutes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $employee = Employee::factory()->create(['name' => 'Ana', 'is_active' => true]);

        TimeEntry::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => '2026-05-10 08:00:00',
            'ended_at' => '2026-05-10 09:30:00',
        ]);

        TimeEntry::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => '2026-04-10 08:00:00',
            'ended_at' => '2026-04-10 09:00:00',
        ]);

        $response = $this->getJson('/api/reports/time-entries?start_date=2026-05-01&end_date=2026-05-31');
        $response->assertOk();
        $response->assertJsonPath('meta.total_minutes', 90);
    }

    public function test_report_can_sort_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $a = Employee::factory()->create(['name' => 'Ana', 'is_active' => true]);
        $b = Employee::factory()->create(['name' => 'Bruno', 'is_active' => true]);

        TimeEntry::factory()->create([
            'employee_id' => $b->id,
            'started_at' => '2026-05-10 08:00:00',
            'ended_at' => '2026-05-10 09:00:00',
        ]);

        TimeEntry::factory()->create([
            'employee_id' => $a->id,
            'started_at' => '2026-05-10 08:00:00',
            'ended_at' => '2026-05-10 09:00:00',
        ]);

        $response = $this->getJson('/api/reports/time-entries?start_date=2026-05-01&end_date=2026-05-31&sort=name&direction=asc');
        $response->assertOk();
        $this->assertSame('Ana', $response->json('data.0.employee_name'));
    }
}

