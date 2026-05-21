<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketResolved;
use App\Jobs\BroadcastTicketAssigned;
use App\Jobs\BroadcastTicketResolved;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Handle the Ticket "creating" event.
     * Auto-clasificación síncrona instantánea antes de guardar en la BD.
     */
    public function creating(Ticket $ticket): void
    {
        // Asignar valores temporales mientras la IA clasifica en segundo plano
        $ticket->ia_categoria = 'Procesando...';
        $ticket->ia_prioridad = 'Procesando...';
        $ticket->ia_resumen = 'Clasificación en progreso...';
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Notificar asignación inicial si ya viene con técnico
        if ($ticket->user_id) {
            $assignedUser = \App\Models\User::find($ticket->user_id);
            if ($assignedUser) {
                $assignedUser->notify(new TicketAssigned($ticket));
                BroadcastTicketAssigned::dispatch($ticket, $assignedUser);
            }
        }

        // Despachar el Job para clasificar con IA de forma asíncrona
        \App\Jobs\ClassifyTicketWithGemini::dispatch($ticket);
    }

    /**
     * Handle the Ticket "updating" event.
     */
    public function updating(Ticket $ticket): void
    {
        if ($ticket->isDirty('estado')) {
            $oldEstado = $ticket->getOriginal('estado');
            $newEstado = $ticket->estado;

            $stateOrder = [
                'Abierto'    => 1,
                'En Proceso' => 2,
                'Resuelto'   => 3,
                'Cerrado'    => 4,
            ];

            if (isset($stateOrder[$oldEstado]) && isset($stateOrder[$newEstado])) {
                if ($stateOrder[$newEstado] < $stateOrder[$oldEstado]) {
                    throw new \Exception("Máquina de estados estricta: No se permite retroceder el ticket de '{$oldEstado}' a '{$newEstado}'.");
                }
            }
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Notificar asignación (DB + Broadcast en tiempo real)
        if ($ticket->wasChanged('user_id') && $ticket->user_id) {
            $assignedUser = \App\Models\User::find($ticket->user_id);
            if ($assignedUser) {
                $assignedUser->notify(new TicketAssigned($ticket));
                // Disparar broadcast en tiempo real vía Job
                BroadcastTicketAssigned::dispatch($ticket, $assignedUser);
            }
        }

        // Notificar resolución (DB + Broadcast en tiempo real)
        if ($ticket->wasChanged('estado') && $ticket->estado === 'Resuelto') {
            $admins = \App\Models\User::where('role', 'Administrador')->get();
            foreach ($admins as $admin) {
                $admin->notify(new TicketResolved($ticket));
            }
            // Disparar broadcast en tiempo real vía Job
            BroadcastTicketResolved::dispatch($ticket);
        }

        if ($ticket->estado === 'Resuelto' && $ticket->latitud_capturada && $ticket->longitud_capturada) {
            $cliente = $ticket->cliente;
            if ($cliente) {
                $cliente->latitud = $ticket->latitud_capturada;
                $cliente->longitud = $ticket->longitud_capturada;
                $cliente->saveQuietly();
            }
        }
    }
}
