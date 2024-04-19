<?php

namespace App\ThrustHelpers\Filters;

use App\TicketPostType;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class TicketPostTypeFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (! $value) {
            return $query;
        }

        return $query->where('ticket_post_type_id', $value);
    }

    public function options()
    {
        return TicketPostType::all()->mapWithKeys(function ($type) {
            return [$type->name => $type->id];
        })->toArray();
    }

    public function getTitle()
    {
        return trans_choice('ticket.postType', 2);
    }
}
