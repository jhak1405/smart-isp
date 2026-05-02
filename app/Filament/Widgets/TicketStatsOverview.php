<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTickets = Ticket::count();
        $resueltos = Ticket::whereIn('estado', ['Resuelto', 'Cerrado'])->count();
        $abiertos = Ticket::where('estado', 'Abierto')->count();
        
        $resolucionRate = $totalTickets > 0 ? round(($resueltos / $totalTickets) * 100, 1) : 0;

        return [
            Stat::make('Total Tickets', $totalTickets)
                ->description('Tickets registrados en el sistema')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('Tasa de Resolución', $resolucionRate . '%')
                ->description($resueltos . ' tickets resueltos o cerrados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Tickets Pendientes', $abiertos)
                ->description('Tickets abiertos sin asignar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->chart([17, 16, 14, 15, 14, 13, 12]),
        ];
    }
}
