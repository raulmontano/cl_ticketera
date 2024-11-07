<?php

namespace App\ThrustHelpers\Filters;

use App\Ticket;
use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;

class CreatedAtFilter extends TextFilter
{
    public function getTitle()
    {
        return __('ticket.created_at');
    }

    public function apply(Request $request, $query, $value)
    {
        if (count($value) == 2 && $value[0] != "" && $value[1] != "") {
            $query->whereBetween('tickets.created_at', [$value[0] . ' 00:00:00',$value[1] . ' 23:59:59']);
        }

        return $query;
    }

    public function display($filtersApplied)
    {
        return view('components.filters.date', [
            'filter' => $this,
            'value'  => $this->filterValue($filtersApplied),
        ])->render();
    }
}
