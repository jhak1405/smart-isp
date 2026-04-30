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
}
