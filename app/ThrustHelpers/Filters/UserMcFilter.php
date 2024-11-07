<?php

namespace App\ThrustHelpers\Filters;

use App\Team;
use App\TicketEvent;
use BadChoice\Thrust\Filters\SelectFilter;
use Illuminate\Http\Request;

class UserMcFilter extends SelectFilter
{
    public function apply(Request $request, $query, $value)
    {
        if (! $value) {
            return $query;
        }

        //COMPLICACIÓN PARA OBTENER EL ULTIMO USUARIO ASIGNADO DEL TIPO MC

        //1. se obtienen todos los tickets que algun momento se asignó el usuario del filtro como MC
        $ticketsByUser = TicketEvent::select('ticket_events.ticket_id')
                                ->whereIn('ticket_events.assigned_to_user_id', $value)
                                ->get()
                                ->pluck('ticket_id');

        //2. Se obtienen todas las asignaciones de los tickets tipo usuario MC
        $tickets = TicketEvent::selectRaw('ticket_events.id,ticket_events.ticket_id,ticket_events.assigned_to_user_id')
                ->join('users', 'users.id', 'ticket_events.assigned_to_user_id')
                ->join('memberships', function ($join) {
                    $join->on('memberships.user_id', '=', 'ticket_events.user_id');
                    $join->on('memberships.team_id', \DB::raw(2));
                })
                ->whereIn('ticket_events.ticket_id', $ticketsByUser)
                ->orderBy('ticket_events.id', 'desc')
                ->get();

        //3. Se ordena para saber cual fué el ultimo usuario MC asignado
        $sorted = $tickets->groupBy('ticket_id')
                            ->map(function ($group) {
                                return $group->sortByDesc('id')->take(1);
                            })
                            ->flatten(1);

        $filteredTickets = [];

        //4. Se iteran las ultimas asignaciones para saber si coincide con el filtro seleccionado
        foreach ($sorted as $ticket) {
            if (in_array($ticket['assigned_to_user_id'], $value)) {
                $filteredTickets[] = $ticket['ticket_id'];
            }
        }

        return $query->whereIn('tickets.id', $filteredTickets);
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
        //MC TEAM IS =1
        return Team::find(2)->members()->get()->mapWithKeys(function ($user) {
            return [$user->name => $user->id];
        })->toArray();
    }

    public function getTitle()
    {
        return 'Usuario MC';
    }
}
