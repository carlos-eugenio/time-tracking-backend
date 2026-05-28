<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório - Registro de horas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 16px; margin: 0 0 12px 0; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f5f5f5; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Relatório - Registro de horas</h1>

    <div class="meta">
        <div>
            <strong>Período:</strong>
            {{ \Carbon\CarbonImmutable::parse($filters['start_date'])->format('d/m/Y') }}
            até
            {{ \Carbon\CarbonImmutable::parse($filters['end_date'])->format('d/m/Y') }}
        </div>
        @if (!empty($filters['employee_id']))
            <div><strong>Funcionário:</strong> #{{ $filters['employee_id'] }}</div>
        @endif
        <div><strong>Total:</strong> {{ $totalMinutes }} minutos</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Funcionário</th>
                <th>Início</th>
                <th>Final</th>
                <th class="right">Minutos</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->employee_name }}</td>
                    <td>{{ optional($row->started_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ optional($row->ended_at)->format('d/m/Y H:i:s') }}</td>
                    <td class="right">
                        @if ($row->ended_at)
                            {{ (int) $row->started_at->diffInMinutes($row->ended_at, true) }}
                        @endif
                    </td>
                    <td>{{ $row->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
