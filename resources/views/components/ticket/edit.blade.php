@if( $ticket->canBeEdited() )
    <div class="float-right mr4" id="edit-ticket-button">

      <button onClick="$('#edit-ticket-button').hide(); $('#ticket-info').hide(); $('#ticket-edit').show()">{{ __('ticket.edit') }}</button>

    </div>
@endif
