<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Auth\UserProvider;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function userProvider(): UserProvider
    {
        return $this->getAuthGuard()->getProvider();
    }

    private function getAuthGuard()
    {
        return $this->container->make('auth')->guard(config('sanctum.guard.0', 'web'));
    }
}
