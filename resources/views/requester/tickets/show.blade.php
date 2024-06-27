@extends('layouts.requester')
@section('content')
    <div class="description comment">
        <h2>#{{  $ticket->reference_number }} - {{ $ticket->title }}</h2>
        <p>{{ $ticket->body }}</p>
        <span class="label ticket-status-{{ $ticket->statusName() }}">{{ __('ticket.' . $ticket->statusName()) }}</span>&nbsp;
        <span class="date">{{  $ticket->created_at->diffForHumans() }}, creado por: {{  $ticket->requester->name }} &lt;{{  $ticket->requester->email }}&gt;</span>
    </div>

    @if(!in_array($ticket->status,[App\Ticket::STATUS_SOLVED,App\Ticket::STATUS_CLOSED,App\Ticket::STATUS_SPAM,App\Ticket::STATUS_ERROR]))
        <div class="comment new-comment">
            {{ Form::open(["url" => route("requester.comments.store",$ticket->public_token)]) }}
            <textarea name="body"></textarea>
            <input type="hidden" name="public" value="1">
            <button class="uppercase ph3"> @busy @icon(comment) ENVIAR {{ __('ticket.comment') }}</button>
            {{ Form::close() }}
        </div>
    @endif
    @include('components.ticketComments', ["comments" => $ticket->comments])
@endsection

@push('edit-scripts')

    <script>
      $(document).ready(function(){
          $('form').submit(function(){
              $(this).children('input[type=submit]').prop('disabled', true);
              $(this).find(':button').prop('disabled', true);
          });
      });
    </script>

@endpush
