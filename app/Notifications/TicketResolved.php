<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolved extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->subject('Ticket resuelto')
            ->line('El ticket "' . $this->ticket->titulo . '" ha sido resuelto.')
            ->action('Ver Ticket', url('/admin/tickets/' . $this->ticket->id . '/edit'))
            ->line('Gracias!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'mensaje' => 'El ticket ha sido resuelto.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'mensaje' => 'El ticket ha sido resuelto.',
        ]);
    }
}
