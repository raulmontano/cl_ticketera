<h4> @icon(inbox) {{ trans_choice('ticket.ticket', 2) }}</h4>
<ul>
    @php ( $repository = new App\Repositories\TicketsRepository )

    @if( auth()->user()->assistant )
        @include('components.sidebarItem', ["url" => route('tickets.index') . "?escalated=true",    "title" => __('ticket.escalated'),  "count" => $repository->escalated()     ->count()])
    @endif
    @include('components.sidebarItem', ["url" => route('tickets.index') . "?all=true",          "title" => __('ticket.all'),       "count" => $repository->all()               ->count()])
    @include('components.sidebarItem', ["url" => route('tickets.index') . "?pending=true",   "title" => __('ticket.pending'), "count" => $repository->pending()        ->count()])
    @include('components.sidebarItem', ["url" => route('tickets.index') . "?solved=true",       "title" => __('ticket.solved')])
    @include('components.sidebarItem', ["url" => route('tickets.index') . "?closed=true",       "title" => __('ticket.closed')])

</ul>
