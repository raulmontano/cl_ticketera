@extends('emails.layout')

@section('body')
        <div style="border-bottom:1px solid #efefef; padding-bottom:10px; margin-left:20px; margin-top:20px;">
            @if( isset($comment) )
                <b> {{ $comment->author()->name }}</b><br>
                <span style="color:gray">{{ $comment->created_at->toDateTimeString() }}</span><br>
                <p>
                    {!! nl2br( strip_tags($comment->body)) !!}
                </p>
            @else
                <b> {{ $ticket->requester->name }}</b><br>
                <span style="color:gray">{{ $ticket->created_at->toDateTimeString() }}</span><br>
                <p>
                    {!! nl2br( strip_tags($ticket->body)) !!}
                </p>
            @endif
        </div>

        <br><br>
        Hola, hemos recibido tu solicitud. Puedes hacer seguimiento mediante el siguiente enlace.
        <br><br>
        <a href="{{$url}}">{{__('notification.answerTicketLink')}}</a>
        <br><br>
        Mail generado automáticamente.

        <span style="color:white">ticket-id:{{$ticket->id}}.</span>

@endsection
