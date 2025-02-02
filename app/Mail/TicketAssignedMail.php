<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticketId;

    /**
     * Create a new event instance.
     */
    public function __construct($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function build()
    {
        return $this->view('emails.ticket_assigned')
            ->with([
                'ticketId' => $this->ticketId,
            ])
            ->subject('Ticket Assigned');
    }
}
