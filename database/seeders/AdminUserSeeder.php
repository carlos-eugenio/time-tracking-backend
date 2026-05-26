<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME', 'Admin');
        $email = env('ADMIN_EMAIL', 'admin@email.com');
        $password = env('ADMIN_PASSWORD', 'adminTeste1234');

        $data = [
            'name' => $name,
            'password' => Hash::make($password),
        ];

        User::query()->updateOrCreate(
            ['email' => $email],
            $data
        );
    }
}
