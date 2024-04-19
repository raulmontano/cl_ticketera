<div class="description actions comment">
    {{ Form::open(["url" => route("{$endpoint}.assign", $object)]) }}
    <table class="w50 no-padding">

        <tr>
            @can("assignToTeam", $object)
                <td>{{ __('ticket.assigned') }}:</td>
                <td>{{ Form::select('user_id', App\Team::membersByTeam(), $object->user_id, ['class' => 'w100']) }}</td>
            @else
                @if ($object->team)
                    <td>{{ __('ticket.editor') }}:</td>
                    <td>{{ Form::select('user_id', createSelectArray( $object->team->members, true,'-- Sin asignación --'), $object->user_id, ['class' => 'w100']) }}</td>
                @endif
            @endcan

            @can("assignToTeam", $object)
                @include('components.assignTeamField', ["team" => $object->team])
            @endcan

            <td class="text-right" colspan="2">
                <button class="ph4 btn btn-primary"> {{ __('ticket.assign') }}</button>
            </td>

        </tr>

    </table>
    {{ Form::close() }}
</div>
