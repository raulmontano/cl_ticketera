<?php

namespace App\ThrustHelpers\Filters;

use App\Ticket;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class PriorityFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('priority', $value);
    }

    public function options()
    {
        return [
            __("ticket.low")     => Ticket::PRIORITY_LOW,
            __("ticket.normal")  => Ticket::PRIORITY_NORMAL,
            __("ticket.high")    => Ticket::PRIORITY_HIGH,
        ];
    }

    public function getTitle()
    {
        return __('ticket.priority');
    }
}
