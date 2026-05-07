<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    // Solo visible en la página de tickets
    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $total   = Ticket::count();
        $abiertos = Ticket::whereIn('estado', ['Abierto', 'En Proceso'])->count();
        $cerrados = Ticket::whereIn('estado', ['Resuelto', 'Cerrado'])->count();

        return [
            Stat::make('Total de Tickets', $total)
                ->description('Todos los tickets')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),

            Stat::make('Tickets Abiertos', $abiertos)
                ->description('Tickets que requieren atención')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),

            Stat::make('Tickets Cerrados', $cerrados)
                ->description('Tickets resueltos exitosamente')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
