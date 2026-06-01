<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_employee_endpoints(): void
    {
        $this->getJson('/api/employees')->assertUnauthorized();
        $this->postJson('/api/employees', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Maria da Silva',
            'email' => 'maria@email.com',
            'document' => '12345678900',
            'job_title' => 'Secretaria',
            'registration_number' => '001',
        ];

        $response = $this->postJson('/api/employees', $payload);
        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Maria da Silva');
        $this->assertDatabaseHas('employees', [
            'email' => 'maria@email.com',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_employee_requires_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/employees', [
            'email' => 'maria@email.com',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name']);
    }

    public function test_employee_email_must_be_unique_when_present(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Employee::factory()->create(['user_id' => $user->id, 'email' => 'maria@email.com']);

        $this->postJson('/api/employees', [
            'name' => 'Maria da Silva',
            'email' => 'maria@email.com',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_list_employees(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Employee::factory()->count(3)->create(['user_id' => $user->id]);
        Employee::factory()->count(2)->create();

        $response = $this->getJson('/api/employees');
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_authenticated_user_can_update_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'name' => 'Maria da Silva',
            'email' => 'maria@email.com',
        ]);

        $this->patchJson("/api/employees/{$employee->id}", [
            'name' => 'Maria da Silva Silva',
        ])->assertOk()->assertJsonPath('data.name', 'Maria da Silva Silva');
    }

    public function test_authenticated_user_can_deactivate_and_activate_employee(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $this->patchJson("/api/employees/{$employee->id}/deactivate")
            ->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->patchJson("/api/employees/{$employee->id}/activate")
            ->assertOk()
            ->assertJsonPath('data.is_active', true);
    }

    public function test_authenticated_user_cannot_access_employee_from_another_user(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $employee = Employee::factory()->create();

        $this->getJson("/api/employees/{$employee->id}")->assertNotFound();
        $this->patchJson("/api/employees/{$employee->id}", ['name' => 'Outro nome'])->assertNotFound();
        $this->deleteJson("/api/employees/{$employee->id}")->assertNotFound();
    }
}
