@if($attachments && $attachments->count() )
    <div class="mt2">
        @foreach( $attachments as $attachment)
          <div class="row">
            @icon(paperclip)&nbsp;
            @if (Storage::getDefaultDriver() == 'google')
                <a href="{{ config('filesystems.disks.google.endpoint') . '/' . config('filesystems.disks.google.bucket') . "/public/storage/attachments/$attachment->path" }}" target="_blank">{{ $attachment->path }}</a>
            @else
                <a href="{{ route('attachments', $attachment) }}" target="_blank">{{ $attachment->path }}</a>
            @endif
          </div>
        @endforeach
    </div>
@else
  <div class="mt2 alert alert-primary row" role="alert">
    <i class="fa fa-warning text-danger"></i>&nbsp;No se cargaron archivos para esta solicitud.
  </div>
@endif
