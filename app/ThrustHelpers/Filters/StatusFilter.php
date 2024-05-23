<?php

namespace App\ThrustHelpers\Filters;

use App\Ticket;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class StatusFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {

        $query->where('status', $value);

        return $query;
    }

    public function options()
    {


        $options = [

            ucfirst(__("ticket.new" ))     => Ticket::STATUS_NEW,
            ucfirst(__("ticket.open" ))    => Ticket::STATUS_OPEN,
            ucfirst(__("ticket.pending" ))    => Ticket::STATUS_PENDING,
            ucfirst(__("ticket.solved" ))    => Ticket::STATUS_SOLVED,
            ucfirst(__("ticket.closed" ))    => Ticket::STATUS_CLOSED,
            ucfirst(__("ticket.spam" ))    => Ticket::STATUS_SPAM,
            ucfirst(__("ticket.paused" ))    => Ticket::STATUS_PAUSED,
            ucfirst(__("ticket.error" ))    => Ticket::STATUS_ERROR,

        ];



        return $options;
    }
}
