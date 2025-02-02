<?php

namespace App\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class TicketStatusChanged
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $newStatus;
    public $note;

    /**
     * Create a new event instance.
     */
    public function __construct($ticket, $newStatus, $note)
    {
        $this->ticket = $ticket;
        $this->newStatus = $newStatus;
        $this->note = $note;
    }
}
