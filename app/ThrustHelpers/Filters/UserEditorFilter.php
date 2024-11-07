<?php

namespace App\ThrustHelpers\Filters;

use App\Team;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class UserEditorFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (! $value) {
            return $query;
        }

        return $query->whereIn('tickets.user_id', $value);
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
        //EDITOR TEAM IS =1
        return Team::find(1)->members()->get()->mapWithKeys(function ($user) {
            return [$user->name => $user->id];
        })->toArray();
    }

    public function getTitle()
    {
        return 'Usuario editor';
    }
}
