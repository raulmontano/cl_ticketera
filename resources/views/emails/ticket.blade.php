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
        Su solicitud ha sido recepcionada exitosamente, pronto tendrás noticias Claves
        <br><br>

        <div style="margin-top:40px">
            <a href="{{$url}}">{{__('notification.answerTicketLink')}}</a>
        </div>

        <br><br>
        Mail generado automáticamente.

        <span style="color:white">ticket-id:{{$ticket->id}}.</span>

@endsection
