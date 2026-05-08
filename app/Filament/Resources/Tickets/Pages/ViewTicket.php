<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Ticket'),
        ];
    }

    protected function resolveRecord(int|string $key): \App\Models\Ticket
    {
        return \App\Models\Ticket::with('cliente', 'tecnico')->findOrFail($key);
    }
}
