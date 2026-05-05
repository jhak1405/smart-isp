<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification
{

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo ticket asignado')
            ->line('Se te ha asignado un nuevo ticket: ' . $this->ticket->titulo)
            ->action('Ver Ticket', url('/admin/tickets/' . $this->ticket->id . '/edit'))
            ->line('Gracias por tu trabajo!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'format' => 'filament',
            'duration' => 'persistent',
            'title' => 'Nuevo ticket asignado',
            'body' => 'Se te ha asignado el ticket: ' . $this->ticket->titulo,
            'ticket_id' => $this->ticket->id,
            'mensaje' => 'Se te ha asignado un nuevo ticket.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'format' => 'filament',
            'title' => 'Nuevo ticket asignado',
            'body' => 'Se te ha asignado el ticket: ' . $this->ticket->titulo,
            'ticket_id' => $this->ticket->id,
            'mensaje' => 'Se te ha asignado un nuevo ticket.',
        ]);
    }
}
