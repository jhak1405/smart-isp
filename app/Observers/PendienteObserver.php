<?php

namespace App\Observers;

use App\Models\Pendiente;
use Filament\Notifications\Notification;

class PendienteObserver
{
    /**
     * Handle the Pendiente "created" event.
     */
    public function created(Pendiente $pendiente): void
    {
        // Si el pendiente nace ya con un técnico asignado
        if ($pendiente->user_id && $pendiente->tecnico) {
            $this->notificarAsignacion($pendiente);
        }
    }

    /**
     * Handle the Pendiente "updated" event.
     */
    public function updated(Pendiente $pendiente): void
    {
        // Si se cambió el campo user_id y ahora tiene un técnico asignado
        if ($pendiente->isDirty('user_id') && $pendiente->user_id && $pendiente->tecnico) {
            $this->notificarAsignacion($pendiente);
        }
    }

    /**
     * Función privada para enviar la notificación (Campanita de la base de datos)
     */
    private function notificarAsignacion(Pendiente $pendiente): void
    {
        $tipo = $pendiente->tipo ?? 'tarea';
        $fecha = $pendiente->fecha_recordatorio->translatedFormat('d \d\e M');
        $clienteNombre = $pendiente->cliente ? $pendiente->cliente->nombre_completo : 'Cliente no especificado';

        Notification::make()
            ->title("Nuevo Pendiente Asignado")
            ->body("Tienes que realizar: {$tipo} al cliente {$clienteNombre} para el {$fecha}.")
            ->icon('heroicon-o-clipboard-document-check')
            ->iconColor('success')
            ->sendToDatabase($pendiente->tecnico);
    }
}
