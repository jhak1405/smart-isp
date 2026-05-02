<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tickets por Estado';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Ticket::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        $labels = $data->pluck('estado')->toArray();
        $counts = $data->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#ef4444', // Abierto -> Red
                        '#f59e0b', // En Proceso -> Amber
                        '#10b981', // Resuelto -> Emerald
                        '#6b7280', // Cerrado -> Gray
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
