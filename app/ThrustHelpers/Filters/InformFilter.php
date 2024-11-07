<?php

namespace App\ThrustHelpers\Filters;

use App\TicketCompany;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class InformFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (! $value) {
            return $query;
        }

        return $query->whereIn('inform', $value);
    }

    public function display($filtersApplied)
    {
        return view('components.filters.checkbox', [
            'filter' => $this,
            'value'  => $this->filterValue($filtersApplied),
        ])->render();
    }

    public function options()
    {
        return ['Sí' => 1, 'No' => 0];
    }

    public function getTitle()
    {
        return trans('ticket.inform');
    }
}
