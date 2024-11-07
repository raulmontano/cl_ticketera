<?php

namespace App\ThrustHelpers\Filters;

use App\TicketCompany;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class CompanyFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (! $value) {
            return $query;
        }

        return $query->whereIn('ticket_company_id', $value);
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
        return TicketCompany::all()->mapWithKeys(function ($company) {
            return [$company->name => $company->id];
        })->toArray();
    }

    public function getTitle()
    {
        return trans('ticket.company');
    }
}
