
    @foreach($comments as $comment)
        @if($comment instanceof App\TicketEvent)
            @include('components.ticketEvent', ["event" => $comment])
        @else
            <div class="comment @if($comment->private) note @endif">
                <div class="date mb2">
                    <div class="pt1"><strong>{{ $comment->author()->name }}</strong> · {{ $comment->created_at->diffForHumans() }}</div>
                </div>
                <div>
                    @if($comment->private) @icon(sticky-note-o) @endif
                    {!! nl2br( strip_tags($comment->body)) !!}
                </div>
            </div>
        @endif
    @endforeach
