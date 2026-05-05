<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssignedToTechnician implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $technician;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, User $technician)
    {
        $this->ticket = $ticket;
        $this->technician = $technician;
        $this->message = "Se te ha asignado el ticket #{$ticket->id}: {$ticket->titulo}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->technician->id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'descripcion' => $this->ticket->descripcion,
            'message' => $this->message,
            'client' => $this->ticket->cliente?->nombre_completo,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ticket.assigned';
    }
}
