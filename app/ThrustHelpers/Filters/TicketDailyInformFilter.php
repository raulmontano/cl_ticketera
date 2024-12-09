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
            ->orWhere(
                function ($query) {
                    $query->whereIn('tickets.id', function ($query2) {
                        $status = [Ticket::STATUS_SOLVED,
                                    Ticket::STATUS_CLOSED,
                                    Ticket::STATUS_SPAM,
                                    Ticket::STATUS_ERROR];

                        $query2->select('comments.ticket_id')
                                ->from('comments')
                                ->whereIn('comments.new_status', $status)
                                ->whereDate('comments.created_at', '=', date('Y-m-d'));
                    });
                }
          );
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
