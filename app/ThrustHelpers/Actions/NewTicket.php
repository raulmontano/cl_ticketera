<?php

namespace App\ThrustHelpers\Actions;

use BadChoice\Thrust\Actions\MainAction;
use App\Ticket;

class NewTicket extends MainAction
{
    public function display($resourceName, $parent_id = null)
    {
        //dump($resourceName, request());

        if (auth()->user()->can('create', Ticket::class)) {
            return "<a class='button btn btn-primary' href=".route('tickets.create').'> '.icon('plus').' '.__('ticket.newTicket').'</a>';
        }
    }
}
