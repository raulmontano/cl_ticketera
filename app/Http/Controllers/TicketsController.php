<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Ticket;
use App\Attachment;
use BadChoice\Thrust\Controllers\ThrustController;

class TicketsController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('tickets');
    }

    /*public function index(TicketsRepository $repository)
    {
        $ticketsQuery = TicketsIndexQuery::get($repository);
        $ticketsQuery = $ticketsQuery->select('tickets.*')->latest('updated_at');

        return view('tickets.index', ['tickets' => $ticketsQuery->paginate(25, ['tickets.user_id'])]);
    }*/

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store()
    {
        $rules = [
          'title'     => 'required|min:3',
          'body'      => 'required',
          'channels'  => 'required|array|min:1',
          'categories'=> 'required|array|min:1',
          'post_type' => 'required|exists:ticket_post_types,id',
          'type'      => 'required|exists:ticket_types,id',
          'company'   => 'required|exists:ticket_companies,id',
          'team_id'   => 'nullable|exists:teams,id',
      ];

        if ($user = \Auth::user()) {
            //
            $requester = ['name' => $user->name, 'email' => $user->email];
        } else {
            $rules[] = ['requester' => 'required|array'];
            $requester = request('requester');
        }

        if (request()->hasFile('attachment')) {
            \Session::flash('alert-type', 'warning');
            \Session::flash('message', 'Selecciona nuevamente los archivos');
        }

        $this->validate(request(), $rules);

        \Session::forget('alert-type');
        \Session::forget('message');

        $ticket = Ticket::createAndNotify(
            $requester,
            request('title'),
            request('body'),
            request('channels'),
            request('categories'),
            request('type'),
            request('company'),
            request('post_type'),
            request('start_date'),
            request('end_date'),
            );

        //FIXME
        if (request('status')) {
            //update
            $ticket->updateStatus(request('status'));
        } else {
            //create
            $ticket->updateStatus(Ticket::STATUS_NEW);
        }

        if (request('team_id')) {
            $ticket->assignToTeam(request('team_id'));
        }

        if ($ticket && request()->hasFile('attachment')) {
            Attachment::storeAttachmentFromRequest(request(), $ticket);
        }

        return redirect()->route('tickets.show', $ticket);
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->updateStatus(Ticket::STATUS_OPEN);

        return back();
    }

    public function update(Ticket $ticket)
    {
        $rules = [
                    'title'     => 'required|min:3',
                    'body'      => 'required',
                    'channels'  => 'required|array|min:1',
                    'categories'=> 'required|array|min:1',
                    'post_type' => 'required|exists:ticket_post_types,id',
                    'type'      => 'required|exists:ticket_types,id',
                    'company'   => 'required|exists:ticket_companies,id',
                    //'team_id'   => 'nullable|exists:teams,id',
                    'priority'  => 'required|integer',
                ];

        $this->validate(request(), $rules);

        $ticket->updateWith(
            request('title'),
            request('body'),
            request('channels'),
            request('categories'),
            request('type'),
            request('company'),
            request('post_type'),
            request('priority'),
            request('start_date'),
            request('end_date'),
            );

        if ($ticket && request()->hasFile('attachment')) {
            Attachment::storeAttachmentFromRequest(request(), $ticket);
        }

        /*
        $notification = array(
                'message' => 'Successfully Done',
                'alert-type' => 'success'
            );*/

        return back()->withMessage('Actualizado');
    }
}
