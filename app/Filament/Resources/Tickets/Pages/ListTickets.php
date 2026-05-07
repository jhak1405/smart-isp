<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Filament\Widgets\TicketStatsWidget;
use App\Models\Ticket;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Ticket'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $userId = auth()->id();

        return [
            'todos' => Tab::make('Todos los Tickets')
                ->icon('heroicon-o-ticket')
                ->badge(Ticket::count()),

            'abiertos' => Tab::make('Abiertos')
                ->icon('heroicon-o-clock')
                ->badge(Ticket::whereIn('estado', ['Abierto', 'En Proceso'])->count())
                ->modifyQueryUsing(fn ($query) => $query->whereIn('estado', ['Abierto', 'En Proceso'])),

            'sin_asignar' => Tab::make('Sin Asignar')
                ->icon('heroicon-o-user-minus')
                ->badge(Ticket::whereNull('user_id')->count())
                ->modifyQueryUsing(fn ($query) => $query->whereNull('user_id')),

            'cerrados' => Tab::make('Cerrados')
                ->icon('heroicon-o-check-circle')
                ->badge(Ticket::whereIn('estado', ['Resuelto', 'Cerrado'])->count())
                ->modifyQueryUsing(fn ($query) => $query->whereIn('estado', ['Resuelto', 'Cerrado'])),
        ];
    }
}
