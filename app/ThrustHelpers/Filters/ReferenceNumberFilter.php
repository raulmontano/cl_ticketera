<?php

namespace App\ThrustHelpers\Filters;

use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;

class ReferenceNumberFilter extends TextFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereRaw("DATE_FORMAT(tickets.created_at,'%Y%m%d_%H%i') like '%{$value}%'");
    }

    public function getTitle()
    {
        return trans('ticket.reference_number');
    }
}
