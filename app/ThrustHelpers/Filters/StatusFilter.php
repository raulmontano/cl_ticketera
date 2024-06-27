<?php

namespace App\ThrustHelpers\Filters;

use App\Ticket;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class StatusFilter extends SelectFilter
{
    public function getTitle()
    {
        return __('ticket.status');
    }

    public function apply(Request $request, $query, $value)
    {
        $query->where('status', $value);

        return $query;
    }

    public function options()
    {
        $isEditor = false;

        if (auth()->user()->teams()->count()) {
            //
            $isEditor = (auth()->user()->teams()->first()->id == 1);
        }

        if ($isEditor) {
            $options = [

              ucfirst(__("ticket.pending"))    => Ticket::STATUS_PENDING,
              ucfirst(__("ticket.solved"))    => Ticket::STATUS_SOLVED,
              ucfirst(__("ticket.paused"))    => Ticket::STATUS_PAUSED,
              ucfirst(__("ticket.error"))    => Ticket::STATUS_ERROR,

          ];
        } else {
            $options = [

              ucfirst(__("ticket.new"))     => Ticket::STATUS_NEW,
              ucfirst(__("ticket.open"))    => Ticket::STATUS_OPEN,
              ucfirst(__("ticket.pending"))    => Ticket::STATUS_PENDING,
              ucfirst(__("ticket.solved"))    => Ticket::STATUS_SOLVED,
              ucfirst(__("ticket.closed"))    => Ticket::STATUS_CLOSED,
              ucfirst(__("ticket.spam"))    => Ticket::STATUS_SPAM,

          ];
        }




        return $options;
    }
}
