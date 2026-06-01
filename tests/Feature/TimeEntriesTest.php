<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TimeEntriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_time_entry_endpoints(): void
    {
        $this->getJson('/api/time-entries')->assertUnauthorized();
        $this->postJson('/api/time-entries', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_time_entry_for_active_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $payload = [
            'employee_id' => $employee->id,
            'started_at' => '2026-05-27 08:00:00',
            'ended_at' => '2026-05-27 17:00:00',
            'notes' => 'Expediente normal',
        ];

        $response = $this->postJson('/api/time-entries', $payload);
        $response->assertCreated();
        $response->assertJsonPath('data.employee_id', $employee->id);

        $this->assertDatabaseHas('time_entries', [
            'employee_id' => $employee->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    public function test_cannot_create_time_entry_for_inactive_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => false]);

        $this->postJson('/api/time-entries', [
            'employee_id' => $employee->id,
            'started_at' => '2026-05-27 08:00:00',
        ])->assertUnprocessable()->assertJsonValidationErrors(['employee_id']);
    }

    public function test_create_time_entry_requires_started_at(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $this->postJson('/api/time-entries', [
            'employee_id' => $employee->id,
        ])->assertUnprocessable()->assertJsonValidationErrors(['started_at']);
    }

    public function test_create_time_entry_requires_ended_at_after_started_at_when_present(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $this->postJson('/api/time-entries', [
            'employee_id' => $employee->id,
            'started_at' => '2026-05-27 10:00:00',
            'ended_at' => '2026-05-27 09:00:00',
        ])->assertUnprocessable()->assertJsonValidationErrors(['ended_at']);
    }

    public function test_authenticated_user_can_list_time_entries_and_filter_by_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employeeA = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        $employeeB = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        $otherEmployee = Employee::factory()->create(['is_active' => true]);

        TimeEntry::factory()->count(2)->create(['employee_id' => $employeeA->id]);
        TimeEntry::factory()->count(3)->create(['employee_id' => $employeeB->id]);
        TimeEntry::factory()->count(4)->create(['employee_id' => $otherEmployee->id]);

        $response = $this->getJson("/api/time-entries?employee_id={$employeeA->id}");
        $response->assertOk();
        $response->assertJsonStructure(['data', 'links', 'meta']);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_authenticated_user_can_update_time_entry_and_sets_updated_by(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        $timeEntry = TimeEntry::factory()->create([
            'employee_id' => $employee->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'started_at' => '2026-05-27 08:00:00',
            'ended_at' => null,
        ]);

        $this->patchJson("/api/time-entries/{$timeEntry->id}", [
            'ended_at' => '2026-05-27 12:00:00',
        ])->assertOk()->assertJsonPath('data.ended_at', '2026-05-27T12:00:00.000000Z');

        $this->assertDatabaseHas('time_entries', [
            'id' => $timeEntry->id,
            'updated_by' => $user->id,
        ]);
    }

    public function test_authenticated_user_cannot_create_time_entry_for_employee_from_another_user(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $employee = Employee::factory()->create(['is_active' => true]);

        $this->postJson('/api/time-entries', [
            'employee_id' => $employee->id,
            'started_at' => '2026-05-27 08:00:00',
        ])->assertUnprocessable()->assertJsonValidationErrors(['employee_id']);
    }

    public function test_authenticated_user_cannot_access_time_entry_from_another_user(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $timeEntry = TimeEntry::factory()->create();

        $this->getJson("/api/time-entries/{$timeEntry->id}")->assertNotFound();
        $this->patchJson("/api/time-entries/{$timeEntry->id}", ['notes' => 'updated'])->assertNotFound();
        $this->deleteJson("/api/time-entries/{$timeEntry->id}")->assertNotFound();
    }
}
