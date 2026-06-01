<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Ticket;
use App\TicketEvent;
use App\Attachment;

class RequesterTicketsController extends Controller
{
    public function show($public_token)
    {
        $ticket = Ticket::findWithPublicToken($public_token);

        $tickets = $ticket->requester->tickets()
                                        ->where('id', '!=', $ticket->id)
                                        ->orderBy('created_at', 'DESC')
                                        ->get();

        return view('requester.tickets.show', ['ticket' => $ticket, 'tickets' => $tickets]);
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

    public function verify(Request $request)
    {
        $params = '';

        $title = $request->get('title');

        if($title){
            $params = '?title='.$title;
        }
        

        return view('requester.tickets.verify',['params' => $params]);
    }

    public function create(Request $request){

        $user = $request->attributes->get('somosclave_user');
        $somosclave_token = $request->attributes->get('somosclave_token');

        return view('requester.tickets.create',['user_name' => $user['name'], 
                                                'user_id' => $user['id'],
                                                'somosclave_token' => $somosclave_token, 
                                                'user_email' => $user['email']]);
    }

    public function store(Request $request)
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
                    'attachment'   => 'nullable|array',
            'attachment.*' => [
                                'file',
                                'mimes:pdf,doc,docx,xls,xlsx,xlsm,ppt,pptx,jpg,jpeg,png,gif,webp,svg',
                            'mimetypes:'
                                . 'application/pdf,'
                                . 'application/msword,'
                                . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document,'
                                . 'application/vnd.ms-excel,'
                                . 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'
                                . 'application/vnd.ms-excel.sheet.macroEnabled.12,'
                                . 'application/vnd.ms-powerpoint,'
                                . 'application/vnd.openxmlformats-officedocument.presentationml.presentation,'
                                . 'image/jpeg,'
                                . 'image/png,'
                                . 'image/gif,'
                                . 'image/webp,'
                                . 'image/svg+xml',
                            ],
      ];      

        $rules[] = ['requester' => 'required|array'];
        $requester = request('requester');

        if (request()->hasFile('attachment')) {
            \Session::flash('alert-type', 'warning');
            \Session::flash('message', 'Selecciona nuevamente los archivos');
        }

        $messages = [
            // Attachments
            'attachment.array'        => 'Los archivos adjuntos no son válidos.',
            'attachment.*.file'       => 'El archivo debe ser válido.',
            'attachment.*.mimes'      => 'Formato no permitido. Solo PDF, Word, Excel, PowerPoint o imágenes.',
            'attachment.*.mimetypes'  => 'Formato no permitido. Solo PDF, Word, Excel, PowerPoint o imágenes.',
        ];

        $this->validate(request(), $rules,$messages);

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

        $somosClaveUser = $request->attributes->get('somosclave_user');
        TicketEvent::make($ticket, 'Usuario origen somosclave.cl: '.$somosClaveUser['id'] .' - ' . $somosClaveUser['name'] . ' ('.$somosClaveUser['email'].')');

        return redirect()->route('requester.tickets.show', $ticket->public_token);
    }

    public function test(Request $request)
    {
        $somosClaveUser = $request->attributes->get('somosclave_user');

        dump($somosClaveUser);
    }
}
