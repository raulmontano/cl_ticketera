<?php

namespace App\Listeners;

use App\Events\TicketStatusUpdated;
use App\Ticket;

class SendToTeamWithTicketStatus
{
    public function handle(TicketStatusUpdated $event)
    {
        if (!in_array($event->previousStatus,[Ticket::STATUS_PENDING,Ticket::STATUS_SOLVED,Ticket::STATUS_ERROR])
            && $event->ticket->status == Ticket::STATUS_PENDING) {
            $event->ticket->user()->dissociate();
            $event->ticket->save();
            $event->ticket->assignToTeam(1); //editores
        }
    }
}
