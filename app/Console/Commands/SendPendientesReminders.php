<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pendiente;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class SendPendientesReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pendientes:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones de los pendientes que vencen mañana o hoy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envío de recordatorios de pendientes...');

        // Fechas a verificar
        $hoy = Carbon::today();
        $manana = Carbon::tomorrow();

        // Buscar todos los pendientes no completados que vencen hoy o mañana
        $pendientes = Pendiente::with(['cliente', 'tecnico'])
            ->where('estado', 'Pendiente')
            ->whereIn('fecha_recordatorio', [$hoy, $manana])
            ->get();

        if ($pendientes->isEmpty()) {
            $this->info('No hay pendientes para notificar hoy o mañana.');
            return;
        }

        $admins = User::where('role', 'Administrador')->get();
        $count = 0;

        foreach ($pendientes as $pendiente) {
            $esManana = $pendiente->fecha_recordatorio->isSameDay($manana);
            $tiempoTxt = $esManana ? 'Mañana' : 'HOY';
            $color = $esManana ? 'warning' : 'danger';
            
            $clienteNombre = $pendiente->cliente ? $pendiente->cliente->nombre_completo : 'Cliente no especificado';
            $tipoPendiente = $pendiente->tipo ?? 'Sin tipo';

            $titulo = "Pendiente para $tiempoTxt: $tipoPendiente";
            $cuerpo = "Cliente: $clienteNombre";

            $notification = Notification::make()
                ->title($titulo)
                ->body($cuerpo)
                ->icon('heroicon-o-clock')
                ->iconColor($color);

            // Si tiene técnico asignado, notificar al técnico
            if ($pendiente->user_id) {
                // Solo enviamos si el usuario existe
                if ($pendiente->tecnico) {
                    $notification->sendToDatabase($pendiente->tecnico);
                    $count++;
                }
            } else {
                // Si NO tiene técnico asignado, notificar a todos los administradores
                foreach ($admins as $admin) {
                    $notification->sendToDatabase($admin);
                    $count++;
                }
            }
        }

        $this->info("Se enviaron $count notificaciones de pendientes exitosamente.");
    }
}
