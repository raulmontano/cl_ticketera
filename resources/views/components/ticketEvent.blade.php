<div class="ticketEvent p1 mb1">
    <div class="">
        {{--@icon(dot-circle-o)--}}
        <strong>{{ $event->author()->name }}</strong>
        •
        {!! nl2br( strip_tags($event->body)) !!}
        •
        {{ $event->created_at->diffForHumans() }}
    </div>
</div>
