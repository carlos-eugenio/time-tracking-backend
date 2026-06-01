<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo.usuario@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'feature-test',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user',
                ],
            ])
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', 'novo.usuario@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'novo.usuario@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'admin@email.com',
            'password' => 'adminTeste1234',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'admin@email.com',
            'password' => 'adminTeste1234',
            'device_name' => 'feature-test',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user',
                ],
            ]);
    }
}
