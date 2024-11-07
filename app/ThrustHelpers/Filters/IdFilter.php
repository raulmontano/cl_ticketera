<?php

namespace App\ThrustHelpers\Filters;

use BadChoice\Thrust\Filters\TextFilter;
use Illuminate\Http\Request;

class IdFilter extends TextFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (!$value) {
            return $query;
        }

        return $query->where("tickets.id", $value);
    }

    public function getTitle()
    {
        return 'ID';
    }
}
