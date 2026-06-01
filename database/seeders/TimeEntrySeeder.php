<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('ADMIN_EMAIL', 'admin@email.com'))
            ->firstOrFail();

        $userId = $admin->id;

        $entriesByRegistration = [
            '1001' => [
                ['2026-05-20 08:00:00', '2026-05-20 17:00:00', 'Expediente normal'],
                ['2026-05-21 08:05:00', '2026-05-21 17:10:00', 'Reuniao de alinhamento no fim do dia'],
                ['2026-05-22 08:00:00', '2026-05-22 16:30:00', 'Saida antecipada autorizada'],
            ],
            '1002' => [
                ['2026-05-20 09:00:00', '2026-05-20 18:00:00', 'Expediente normal'],
                ['2026-05-21 09:10:00', '2026-05-21 18:05:00', 'Fechamento financeiro'],
                ['2026-05-22 09:00:00', '2026-05-22 17:45:00', null],
            ],
            '1003' => [
                ['2026-05-20 08:30:00', '2026-05-20 17:30:00', 'Implementacao de API'],
                ['2026-05-21 08:30:00', '2026-05-21 17:45:00', 'Ajustes em testes'],
                ['2026-05-22 08:45:00', null, 'Ponto de entrada aberto para teste'],
            ],
            '1004' => [
                ['2026-05-20 08:00:00', '2026-05-20 17:00:00', 'Entrevistas internas'],
                ['2026-05-21 08:15:00', '2026-05-21 17:20:00', 'Treinamento de equipe'],
                ['2026-05-22 08:10:00', '2026-05-22 16:50:00', null],
            ],
            '1005' => [
                ['2026-05-20 07:45:00', '2026-05-20 16:45:00', 'Atendimento de chamados'],
                ['2026-05-21 08:00:00', '2026-05-21 17:00:00', null],
                ['2026-05-22 08:00:00', '2026-05-22 17:30:00', 'Plantao estendido'],
            ],
            '1006' => [
                ['2026-05-20 09:00:00', '2026-05-20 18:15:00', 'Planejamento do projeto'],
                ['2026-05-21 09:00:00', '2026-05-21 18:00:00', null],
                ['2026-05-22 09:15:00', '2026-05-22 18:10:00', 'Revisao de cronograma'],
            ],
            '1007' => [
                ['2026-05-20 08:20:00', '2026-05-20 17:20:00', 'Visita a cliente'],
                ['2026-05-21 08:30:00', '2026-05-21 17:30:00', null],
                ['2026-05-22 08:25:00', '2026-05-22 17:15:00', 'Follow-up comercial'],
            ],
        ];

        foreach ($entriesByRegistration as $registrationNumber => $entries) {
            $employee = Employee::query()
                ->where('user_id', $userId)
                ->where('registration_number', $registrationNumber)
                ->first();

            if (!$employee) {
                continue;
            }

            foreach ($entries as [$startedAt, $endedAt, $notes]) {
                TimeEntry::query()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'started_at' => CarbonImmutable::parse($startedAt),
                    ],
                    [
                        'ended_at' => $endedAt ? CarbonImmutable::parse($endedAt) : null,
                        'notes' => $notes,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }
        }
    }
}
