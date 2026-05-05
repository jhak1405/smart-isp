<?php

namespace App\Jobs;

use App\Events\TicketAssignedToTechnician;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastTicketAssigned implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;
    public $technician;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket, User $technician)
    {
        $this->ticket = $ticket;
        $this->technician = $technician;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new TicketAssignedToTechnician($this->ticket, $this->technician));
    }
}
