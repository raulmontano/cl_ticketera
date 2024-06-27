<div class="comment">
    {{ Form::open(["url" => route("{$endpoint}.assign", $object),"id" => "assign-form"]) }}
    <div class="form-row">
        <div class="col-md-8">
            @can("assignToTeam", $object)
                {{ __('ticket.assigned') }}:
                {{ Form::select('user_id', App\Team::membersByTeam(), $object->user_id, ['class' => 'w100']) }}
            @else
                @if ($object->team)
                    {{ __('ticket.editor') }}:
                    @if($object->user_id)
                      {{ Form::select('user_id', createSelectArray( $object->team->members), $object->user_id, ['class' => 'w100']) }}
                    @else
                      {{ Form::select('user_id', createSelectArray( $object->team->members, true,'-- Sin asignación --'), $object->user_id, ['class' => 'w100']) }}
                    @endif
                @endif
            @endcan

            @can("assignToTeam", $object)
                @include('components.assignTeamField', ["team" => $object->team])
            @endcan

        </div>
        <div class="col-md-4">
          <button class="ph4 btn btn-primary"> {{ __('ticket.assign') }}</button>
        </div>

    </div>
    {{ Form::close() }}
</div>

@push('edit-scripts')

    <script>
      $(document).ready(function(){

          $('form#assign-form').submit(function(){

              $(this).children('input[type=submit]').prop('disabled', true);
              $(this).find(':button').prop('disabled', true);
          });
      });
    </script>

@endpush
