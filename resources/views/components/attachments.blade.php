@if($attachments && $attachments->count() )
    <ul class="mt2">
        @foreach( $attachments as $attachment)
          <li>
            @icon(paperclip)&nbsp;
            @if (Storage::getDefaultDriver() == 'google')
                <a href="{{ config('filesystems.disks.google.endpoint') . '/' . config('filesystems.disks.google.bucket') . "/public/storage/attachments/$attachment->path" }}" target="_blank">{{ $attachment->path }}</a>
            @else
                <a href="{{ route('attachments', $attachment) }}" target="_blank">{{ $attachment->path }}</a>

                @if(isset($showDelete) && $showDelete)
                <a class="delete-resource2 thrust-delete2" href="{{ route('attachments', $attachment) }}">&nbsp;@icon(trash)</a>
                @endif

            @endif
          </li>
        @endforeach
    </ul>

@else
  <div class="mt2 alert alert-primary" role="alert">
    <i class="fa fa-warning text-danger"></i>&nbsp;No se cargaron archivos para esta solicitud.
  </div>
@endif

@section('scripts')

<script>

var csrf_token = "{{ csrf_token() }}";

$(function() {

$(".delete-resource2, .delete-resource-simple2").on('click',function(e){
    if (! confirm("¿Deseas eliminarlo?")){ return false; }
    else{
        e.preventDefault();
        var url = $(this).attr('href');

        var urlThrust = url.replace('attachments','thrust/attachments');

        $('<form action="' + urlThrust + '" method="POST"><input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="' + csrf_token + '"></form>').appendTo('body').submit();
    }
});
// Handler for .ready() called.
});
</script>

@endsection
