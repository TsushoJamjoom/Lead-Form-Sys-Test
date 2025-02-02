<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Mail\TicketStatusChangedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketStatusChangedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TicketStatusChanged $event): void
    {
        try {
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

            $ticketId = $event->ticket->id;
            $customerName = $event->ticket->company->company_name;
            $subject = "TICKET UPDATE: $customerName - Ticket (ID: $ticketId)";
            $assignedName = !empty($event->ticket->user) ? $event->ticket->user->name : "All Sales";
            $statusClass = $event->newStatus == 0 ? 'bg-pending' : ($event->newStatus == 1 ? 'bg-progress' : 'bg-success');
            $statusText = $event->newStatus == 0 ? 'Pending' : ($event->newStatus == 1 ? 'In-Progress' : 'Completed');

            // Send To Assigned
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(env('SENDGRID_FROM_EMAIL'), env('SENDGRID_FROM_NAME'));
            $email->setSubject($subject);
            $userEmails = [];
            if (!empty($event->ticket->user)) {
                $email->addTo($event->ticket->user->email);
                $userEmails[] = $event->ticket->user->email;
            } else {
                $users = User::where('department_id', $event->ticket->dept_id)
                    ->where('id', '!=', $event->ticket->created_by)
                    ->get();
                if ($users->isEmpty()) {
                    return;
                }
                foreach ($users as $user) {
                    $email->addTo($user->email);
                }
                $userEmails[] = $users->pluck('email');
            }
            $email->addContent(
                "text/html",
                view('emails.ticket-status-update')
                    ->with([
                        'customerName' => $customerName,
                        'ticketId' => $ticketId,
                        'name' => $assignedName,
                        'statusClass' => $statusClass,
                        'statusText' => $statusText,
                        'note'   => $event->note,
                        'assignedName' => $assignedName,
                    ])->render()
            );
            $sendgridResponse = $sendgrid->send($email);
            Log::info('Ticket status changed mail response:', ['user' => $userEmails, 'ticket' => $event->ticket, 'response' => json_decode($sendgridResponse->body(), true)]);
            // Send To Created By
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(env('SENDGRID_FROM_EMAIL'), env('SENDGRID_FROM_NAME'));
            $email->setSubject($subject);
            $email->addTo($event->ticket->createdUser->email);
            $email->addContent(
                "text/html",
                view('emails.ticket-status-update')
                    ->with([
                        'customerName' => $customerName,
                        'ticketId' => $ticketId,
                        'name' => $event->ticket->createdUser->name,
                        'statusClass' => $statusClass,
                        'statusText' => $statusText,
                        'note'   => $event->note,
                        'assignedName' => $assignedName,
                    ])->render()
            );
            $sendgridResponse = $sendgrid->send($email);
            Log::info('Ticket status changed mail response:', ['user' => $event->ticket->createdUser->email, 'ticket' => $event->ticket, 'response' => json_decode($sendgridResponse->body(), true)]);
        } catch (\Exception $e) {
            Log::error('Error sending ticket status changed notification :', ['error' => $e->getMessage()]);
        }
    }
}
