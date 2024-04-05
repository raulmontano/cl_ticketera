@if($attachments && $attachments->count() )
    <div class="mt2">
        @foreach( $attachments as $attachment)
          <div class="row">
            @icon(paperclip)&nbsp;
            @if (Storage::getDefaultDriver() == 'google')
                <a href="{{ config('filesystems.disks.google.endpoint') . '/' . config('filesystems.disks.google.bucket') . "/public/storage/attachments/$attachment->path" }}" target="_blank">{{ $attachment->path }}</a>
            @else
                @if (Str::endsWith($attachment->path, ['.png', '.jpg', '.pdf', '.mov', '.mp4']))
                    <a href="{{ route('attachments', $attachment->path) }}" target="_blank">{{ $attachment->path }}</a>
                @else
                    <a href="{{ Storage::url("storage/attachments/$attachment->path")}}" target="_blank">{{ $attachment->path }}</a>
                @endif
            @endif
          </div>
        @endforeach
    </div>
@else
  <div class="mt2 alert alert-primary row" role="alert">
    <i class="fa fa-warning text-danger"></i>&nbsp;No se cargaron archivos para esta solicitud.
  </div>
@endif
