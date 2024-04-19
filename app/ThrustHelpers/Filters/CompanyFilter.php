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

      return $query->where('ticket_company_id', $value);
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
