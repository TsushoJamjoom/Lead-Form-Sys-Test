<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Mail\TicketAssignedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TicketAssigned $event): void
    {
        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(env('SENDGRID_FROM_EMAIL'), env('SENDGRID_FROM_NAME'));
            $email->setSubject("Ticket Assigned");
            foreach($event->emails as $toMail){
                $email->addTo($toMail);
            }
            $email->addContent(
                "text/html", view('emails.ticket_assigned')->with([
                    'ticketId' => $event->ticketId,
                ])->render()
            );
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            $sendgrid->send($email);
            // Mail::to($event->emails)->send(new TicketAssignedMail($event->ticketId));
            Log::info('Ticket assigned notification sent to user:', ['users' => $event->emails, 'ticketId' => $event->ticketId]);
        } catch (\Exception $e) {
            Log::error('Error sending ticket assigned notification :', ['error' => $e->getMessage(), 'users' => $event->emails, 'ticketId' => $event->ticketId]);
        }
    }
}
