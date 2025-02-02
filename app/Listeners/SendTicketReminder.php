<?php

namespace App\Listeners;

use App\Events\TicketReminder;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTicketReminder implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketReminder $event): void
    {
        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(env('SENDGRID_FROM_EMAIL'), env('SENDGRID_FROM_NAME'));
            $email->setSubject("Ticket Reminder");
            $userName = '';
            if ($event->ticket->user_id == 0) {
                //$userName = @$event->ticket->dept->name ? 'All ' . $event->ticket->dept->name : "";
                $userName = optional($event->ticket->dept)->name ? 'All ' . optional($event->ticket->dept)->name : "";
                $recipientEmails = User::select('name', 'email')->where('department_id', $event->ticket->dept_id)
                    ->where('id', '!=', $event->ticket->created_by)
                    ->get();
                foreach ($recipientEmails as $toMail) {
                    $email->addTo($toMail->email);
                }
            } else {
                $userName = $event->ticket->user->name;
                $email->addTo($event->ticket->user->email);
            }
            $email->addContent(
                "text/html",
                view('emails.ticket_reminder')->with([
                    'ticketId' => $event->ticket->id,
                    'user_name' => $userName,
                    'company_name' => $event->ticket->company->company_name,
                    'note' => $event->ticket->note,
                ])->render()
            );
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            $sendgrid->send($email);
            // Mail::to($event->emails)->send(new TicketAssignedMail($event->ticketId));
            Log::info('Ticket assigned notification sent to user:', ['users' => $event, 'ticketId' => $event->ticket->id]);
        } catch (\Exception $e) {
            Log::error('Error sending ticket assigned notification :', ['error' => $e->getMessage(), 'users' => $event, 'ticketId' => $event->ticket->id]);
        }
    }
}
