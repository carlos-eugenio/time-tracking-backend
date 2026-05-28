<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Joao Pedro Almeida',
                'email' => 'joao.almeida@email.com',
                'document' => '12345678901',
                'job_title' => 'Analista Administrativo',
                'registration_number' => '1001',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Eduarda Santos',
                'email' => 'maria.santos@email.com',
                'document' => '23456789012',
                'job_title' => 'Assistente Financeiro',
                'registration_number' => '1002',
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Henrique Costa',
                'email' => 'carlos.costa@email.com',
                'document' => '34567890123',
                'job_title' => 'Desenvolvedor Backend',
                'registration_number' => '1003',
                'is_active' => true,
            ],
            [
                'name' => 'Ana Carolina Lima',
                'email' => 'ana.lima@email.com',
                'document' => '45678901234',
                'job_title' => 'Coordenadora de RH',
                'registration_number' => '1004',
                'is_active' => true,
            ],
            [
                'name' => 'Rafael Oliveira Martins',
                'email' => 'rafael.martins@email.com',
                'document' => '56789012345',
                'job_title' => 'Analista de Suporte',
                'registration_number' => '1005',
                'is_active' => true,
            ],
            [
                'name' => 'Juliana Ferreira Rocha',
                'email' => 'juliana.rocha@email.com',
                'document' => '67890123456',
                'job_title' => 'Gerente de Projetos',
                'registration_number' => '1006',
                'is_active' => true,
            ],
            [
                'name' => 'Bruno Araujo Ribeiro',
                'email' => 'bruno.ribeiro@email.com',
                'document' => '78901234567',
                'job_title' => 'Analista Comercial',
                'registration_number' => '1007',
                'is_active' => true,
            ],
            [
                'name' => 'Patricia Gomes Pereira',
                'email' => 'patricia.pereira@email.com',
                'document' => '89012345678',
                'job_title' => 'Assistente Administrativo',
                'registration_number' => '1008',
                'is_active' => false,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::query()->updateOrCreate(
                ['registration_number' => $employee['registration_number']],
                $employee
            );
        }
    }
}
