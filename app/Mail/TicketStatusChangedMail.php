<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $newStatus;
    public $note;
    public $name;

    /**
     * Create a new event instance.
     */
    public function __construct($ticket, $name, $newStatus, $note)
    {
        $this->ticket = $ticket;
        $this->name = $name;
        $this->newStatus = $newStatus;
        $this->note = $note;
    }

    public function build()
    {
        return $this->view('emails.ticket_status_changed')
            ->with([
                'ticket' => $this->ticket,
                'name' => $this->name,
                'status' => $this->newStatus == 0 ? 'Pending' : ($this->newStatus == 1 ? 'In-Progress' : 'Completed'),
                'note'   => $this->note,
            ])
            ->subject('Ticket Status Changed');
    }
}
