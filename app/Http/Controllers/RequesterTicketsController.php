<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Ticket;
use App\Attachment;

class RequesterTicketsController extends Controller
{
    public function show($public_token)
    {
        $ticket = Ticket::findWithPublicToken($public_token);

        return view('requester.tickets.show', ['ticket' => $ticket]);
    }

    public function rate($public_token)
    {
        $ticket = Ticket::findWithPublicToken($public_token);
        $rated  = $ticket->rate(request('rating'));
        if (! $rated) {
            app()->abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Could not rate this ticket');
        }

        return view('requester.tickets.rated', ['ticket' => $ticket]);
    }

    public function create()
    {
        return view('requester.tickets.create');
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


        $rules[] = ['requester' => 'required|array'];
        $requester = request('requester');

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

        //create
        $ticket->updateStatus(Ticket::STATUS_NEW);

        if ($ticket && request()->hasFile('attachment')) {
            Attachment::storeAttachmentFromRequest(request(), $ticket);
        }

        return redirect()->route('requester.tickets.show', $ticket->public_token);
    }
}
