<?php

namespace App\Http\Controllers;

use App\Ticket;

class TicketsAssignController extends Controller
{
    public function store(Ticket $ticket)
    {
        if (request('team_id')) {
            $this->authorize('assignToTeam', $ticket);
            $ticket->assignToTeam(request('team_id'));
        }
        if (request('user_id')) {
            $ticket->assignTo(request('user_id'));

            //FIXME if assignto is the current user, go to show, then go to index
            if (auth()->user()->id == request('user_id')) {
                return redirect()->route('tickets.show', $ticket);
            }
        }

        return redirect()->route('tickets.index');
    }
}
