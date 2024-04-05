<?php

namespace App\Listeners;

use App\Events\TicketStatusUpdated;
use App\Ticket;

class SendToTeamWithTicketStatus
{
    public function handle(TicketStatusUpdated $event)
    {
        if ($event->ticket->status == Ticket::STATUS_PENDING) {
            $event->ticket->user()->dissociate();
            $event->ticket->save();
            $event->ticket->assignToTeam(1); //editores
        }
    }
}
