<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Requester;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        if (isset($notifiable->settings) && $notifiable->settings->new_ticket_notification == false) {
            $return = [];
        } else {
          $return = (method_exists($notifiable, 'routeNotificationForSlack') && $notifiable->routeNotificationForSlack() != null) ? ['slack'] : ['mail'];
        }

        return $return;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

      $url = $notifiable instanceof Requester ? route('requester.tickets.show', $this->ticket->public_token) : route('tickets.show', $this->ticket);

        $mail = (new MailMessage)
            ->subject(__('notification.newTicket').": #{$this->ticket->reference_number}: {$this->ticket->title}")
            ->replyTo($this->ticket->requester->email)
            ->view('emails.ticket', [
                    'title'  => __('notification.newTicketCreated'),
                    'ticket' => $this->ticket,
                    'url'     => $url,
                ]
            );

        if ($this->ticket->requester->email) {
            //$mail->from($this->ticket->requester->email, $this->ticket->requester->name);
            $mail->from(config('mail.fetch.username'), $this->ticket->requester->name);
        }

        return $mail;
    }

    public function toSlack($notifiable)
    {
        return (new BaseTicketSlackMessage($this->ticket, $notifiable))
                ->content(__('notification.ticketCreated'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
