<?php

namespace App\ThrustHelpers\Filters;

use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;
use App\Ticket;

class TicketDailyInformFilter extends TextFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('tickets.created_at', '=', date('Y-m-d'))
                      ->whereIn('tickets.status', [Ticket::STATUS_PENDING,Ticket::STATUS_SOLVED,Ticket::STATUS_PAUSED,Ticket::STATUS_ERROR]);
    }

    public function display($filtersApplied)
    {
        return "";
    }

    public function getTitle()
    {
        return 'Informe del día';
    }
}
