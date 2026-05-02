<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Tickets por Categoría (IA)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Ticket::select('ia_categoria', DB::raw('count(*) as total'))
            ->whereNotNull('ia_categoria')
            ->groupBy('ia_categoria')
            ->get();

        $labels = $data->pluck('ia_categoria')->toArray();
        $counts = $data->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // violet
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
