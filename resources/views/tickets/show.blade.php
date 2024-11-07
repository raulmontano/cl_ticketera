@extends('layouts.app')
@section('content')
    <div class="description container">
        <div class="breadcrumb">
            <a href="{{ route('tickets.index') }}">&lt; {{ trans_choice('ticket.ticket', 2) }}</a>
        </div>
        <div class="card">
          <h3 class="card-header">
            #{{$ticket->reference_number}} · {{ $ticket->title }}
            <a class="ml4 float-right" title="Vista pública" target="_blank" href="{{route('requester.tickets.show',$ticket->public_token)}}"> @icon(globe) </a>

            @if($ticket->team->id == 1)
              <div class="text-muted fs2"><b>{{  $ticket->requester->name }}</b> &lt;{{$ticket->requester->email}}&gt; enviado a editores el {{ $ticket->time_in_editor->first()->created_at->format('Y-m-d H:i:s') }} (<b>{{ $ticket->created_at->diffForHumans() }})</b></div>
            @else
              <div class="text-muted fs2"><b>{{  $ticket->requester->name }}</b> &lt;{{$ticket->requester->email}}&gt; creado el {{ $ticket->created_at->format('Y-m-d H:i:s') }} (<b>{{ $ticket->created_at->diffForHumans() }})</b></div>
            @endif

          </h3>
            <div class="card-body">
              <p class="card-text">{!! nl2br($ticket->body) !!}</p>
            </div>
            <div class="card-footer">

              @include('components.ticket.header')
              @include('components.ticket.merged')

              @if( $ticket->canBeEdited() )
                  <div id="edit-ticket-button" class="float-right mr4">

                    <button class="btn btn-primary" onClick="$('#edit-ticket-button').hide(); $('#ticket-info').hide(); $('#ticket-edit').show()">{{ __('ticket.edit') }}</button>

                  </div>
              @endif

            </div>
        </div>
    </div>

    @if(!in_array($ticket->status,[\App\Ticket::STATUS_SOLVED,\App\Ticket::STATUS_CLOSED,\App\Ticket::STATUS_SPAM,\App\Ticket::STATUS_ERROR]))
    <div class="container">

      <div class="row">

        @can("assignToUser", $ticket)
        <div class="col-md-4">
          <div class="actions card mb-3">
            @include('components.assignActions', ["endpoint" => "tickets", "object" => $ticket])
          </div>
        </div>
        @endcan


        @can("addComments", $ticket)
        <div class="col-md-8">
          <div class="actions card mb-3">
            {{ Form::open(["url" => route("comments.store", $ticket) , "files" => true, "id" => "comment-form"]) }}

            <div class="form-row comment">

                  @can("updateStatus", $ticket)
                  <div class="col-md-3">
                      <label>{{ trans_choice('ticket.status', 1)}}:</label>
                      <select id="new_status" name="new_status">
                          @if($ticket->team->id == 2)
                            <option value="{{\App\Ticket::STATUS_NEW}}"  @if($ticket->status == App\Ticket::STATUS_NEW) selected @endif>{{ __("ticket.new" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_OPEN}}"  @if($ticket->status == App\Ticket::STATUS_OPEN) selected @endif>{{ __("ticket.open" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_PENDING}}" @if($ticket->status == App\Ticket::STATUS_PENDING) selected disabled @endif>{{ __("ticket.pending" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_SOLVED}}"  @if($ticket->status == App\Ticket::STATUS_SOLVED) selected @endif>{{ __("ticket.solved" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_CLOSED}}"  @if($ticket->status == App\Ticket::STATUS_CLOSED) selected @endif>{{ __("ticket.closed" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_SPAM}}"  @if($ticket->status == App\Ticket::STATUS_SPAM) selected @endif>{{ __("ticket.spam" ) }}</option>
                          @else
                            <option value="{{\App\Ticket::STATUS_PENDING}}"  @if($ticket->status == App\Ticket::STATUS_PENDING) selected @endif>{{ __("ticket.pending" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_SOLVED}}"  @if($ticket->status == App\Ticket::STATUS_SOLVED) selected @endif>{{ __("ticket.solved" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_PAUSED}}"  @if($ticket->status == App\Ticket::STATUS_PAUSED) selected @endif>{{ __("ticket.paused" ) }}</option>
                            <option value="{{\App\Ticket::STATUS_ERROR}}"  @if($ticket->status == App\Ticket::STATUS_ERROR) selected @endif>{{ __("ticket.error" ) }}</option>
                          @endif

                      </select>
                    </div>
                  @endcan
              <div class="col-md-6">
                  <textarea id="comment-text-area" class="w100" rows="3" placeholder="Mensaje (opcional)" name="body">@if(auth()->user()->settings->tickets_signature)&#13;&#13;{{ auth()->user()->settings->tickets_signature }}@endif</textarea>

                  {{ Form::checkbox('public') }} Comunicar al solicitante
                  <br>

                </div>
            <div class="col-md-3 d-flex">
                @can("updateStatus", $ticket)
                    <button class="ph3 ml1 btn btn-primary align-self-center">{{ __('ticket.update') }} {{ __('ticket.status')}}</button>
                    @else
                    <button class="ph3 ml1 btn btn-primary align-self-center">Enviar mensaje</button>
                    @endcan
            </div>
            </div>

            {{ Form::close() }}

          </div>
        </div>
        @endcan
        </div>
      </div>
      @endif

<div class="description container">
  <div class="card">
    <h3 class="card-header">Historial de la solicitud</h3>
    @include('components.ticketComments', ["comments" => $ticket->commentsAndNotesAndEvents()->sortBy('created_at')->reverse() ])
  </div>
    </div>

@endsection


@push('edit-scripts')

    <script>
      $(document).ready(function(){

          $('form#comment-form').submit(function(){

              $(this).children('input[type=submit]').prop('disabled', true);
              $(this).find(':button').prop('disabled', true);
          });
      });
    </script>

@endpush
