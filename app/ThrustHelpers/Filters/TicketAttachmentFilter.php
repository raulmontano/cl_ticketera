<?php

namespace App\ThrustHelpers\Filters;

use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;
use App\Ticket;

class TicketAttachmentFilter extends TextFilter
{
    public function apply(Request $request, $query, $value)
    {

        return $query->whereHasMorph('attachable',[Ticket::class], function ($query2) use ($value) {
                    $query2->where('tickets.title', 'like', "%{$value}%");
                  });

    }

    public function getTitle()
    {
        return trans('ticket.subject');
    }
}
