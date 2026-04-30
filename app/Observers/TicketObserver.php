<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Jobs\ClassifyTicketWithGemini;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     * Encola el trabajo de auto-clasificación en segundo plano.
     */
    public function created(Ticket $ticket): void
    {
        // Enviamos el ticket a la cola para no trabar al usuario
        ClassifyTicketWithGemini::dispatch($ticket);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Si el ticket se marcó como Resuelto y tiene coordenadas
        if ($ticket->estado === 'Resuelto' && $ticket->latitud_capturada && $ticket->longitud_capturada) {
            $cliente = $ticket->cliente;
            if ($cliente) {
                // Actualizamos las coordenadas maestras del cliente
                $cliente->latitud = $ticket->latitud_capturada;
                $cliente->longitud = $ticket->longitud_capturada;
                $cliente->saveQuietly();
            }
        }
    }
}
