@extends('layouts.app')
@section('content')
    <div class="description">
        <div class="breadcrumb">
            <a href="{{ route('tickets.index') }}">&lt; {{ trans_choice('ticket.ticket', 2) }}</a>
        </div>
        <div class="card">
          <h3 class="card-header">
            {{ $ticket->title }} <div class="text-muted fs2"><b>{{  $ticket->requester->name }}</b> &lt;{{$ticket->requester->email}}&gt; creado el {{ $ticket->created_at->format('Y-m-d H:i:s') }} (<b>{{ $ticket->created_at->diffForHumans() }})</b></div>
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

    <div class="description">

        @can("assignToUser", $ticket)
        <div class="actions card mb-3">
          @include('components.assignActions', ["endpoint" => "tickets", "object" => $ticket])
        </div>
        @endcan

        <div class="actions card">
            {{ Form::open(["url" => route("comments.store", $ticket) , "files" => true, "id" => "comment-form"]) }}

            <table class="w80 no-padding">

                <tr>
                  @can("updateStatus", $ticket)
                  <td class="p2">
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
                  </td>
                  @endcan
                <td class="w10 p2">
                  <textarea id="comment-text-area" cols="40" rows="3" placeholder="Mensaje (opcional)" name="body" cols="20">@if(auth()->user()->settings->tickets_signature)&#13;&#13;{{ auth()->user()->settings->tickets_signature }}@endif</textarea>
                </td>
            <td >
              @can("updateStatus", $ticket)
                  <button class="ph3 ml1 btn btn-primary">{{ __('ticket.update') }} {{ __('ticket.status')}}</button>
                  @else
                  <button class="ph3 ml1 btn btn-primary">Enviar mensaje</button>
                  @endcan
            </td>

          </tr>

          </table>

            {{ Form::close() }}
        </div>
      </div>


    @include('components.ticketComments', ["comments" => $ticket->commentsAndNotesAndEvents()->sortBy('created_at')->reverse() ])
@endsection
