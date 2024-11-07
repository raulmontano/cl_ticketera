<?php

namespace App\ThrustHelpers\Filters;

use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;
use App\Ticket;

class TicketAttachmentReferenceNumberFilter extends TextFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereHasMorph('attachable', [Ticket::class], function ($query2) use ($value) {
            $query2->whereRaw("DATE_FORMAT(tickets.created_at,'%Y%m%d_%H%i%s') like '%{$value}%'");
        });
    }

    public function getCssDiv()
    {
        return 'col-md-6';
    }

    public function getTitle()
    {
        return trans('ticket.reference_number');
    }
}
