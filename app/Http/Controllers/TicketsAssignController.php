<?php

namespace App\Http\Controllers;

use App\Ticket;

class TicketsAssignController extends Controller
{
    public function store(Ticket $ticket)
    {
        if (request('team_id')) {
            $this->authorize('assignToTeam', $ticket);
            $ticket->assignToTeam(request('team_id'));
        }
        if (request('user_id')) {
            $ticket->assignTo(request('user_id'));

            //FIXME if assignto is the current user, go to show, then go to index
            if (auth()->user()->id == request('user_id')) {
                return redirect()->route('tickets.show', $ticket)->withMessage('Ticket asignado');
            }
        }
        return redirect()->route('tickets.show', $ticket)->withMessage('Ticket asignado');
    }

    public function assignContentId(Ticket $ticket)
    {
        $data = request()->all();

        preg_match_all('/ID\s(\d+)\s-\s/', $ticket->title, $matches);

        if (isset($data['content_id'])) {
            if ($matches && is_array($matches) && count($matches) == 2 && !$matches[1]) {
                //asignar

                $ticket->title = 'ID ' . $data['content_id'] . ' - ' .$ticket->title;
                $ticket->save();
            } else {
                //actualizar
                $ticket->title = str_replace('ID ' . $ticket->getContentId() . ' -', 'ID ' . $data['content_id'] . ' -', $ticket->title);
                $ticket->save();
            }

            return redirect()->route('tickets.show', $ticket)->withMessage('Id asignado');
        } else {
            return redirect()->route('tickets.show', $ticket)->withErrors('No se especificó ningún ID');
        }
    }
}
