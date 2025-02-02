<?php

namespace App\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class TicketAssigned
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $emails;
    
    public $ticketId;

    /**
     * Create a new event instance.
     */
    public function __construct($emails, $ticketId)
    {
        $this->emails = $emails;
        $this->ticketId = $ticketId;
    }
}
