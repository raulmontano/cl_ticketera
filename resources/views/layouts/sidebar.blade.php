<div class="sidebar" id="sidebar">
    <div class="show-mobile absolute ml2 mt-2 fs3">
        <span class="fs3 white" onclick="toggleSidebar()">X</span>
    </div>
    <img src="{{ url("/images/logo_grey.png") }}">
    @include('layouts.sidebar.tickets')

    @if (auth()->user()->can_see_reports)
    <h4> @icon(bar-chart) {{ trans_choice('report.report', 2) }}</h4>
    <ul>
            @include('components.sidebarItem', ["url" => route('reports.index'), "title" => trans_choice('report.report', 2) ])
            @include('components.sidebarItem', ["url" => route('reports.analytics'), "title" => trans_choice('report.analytics', 1) ])
    </ul>
    @endif

@if(auth()->user()->admin)
    <h4> @icon(cog) {{ trans_choice('admin.admin',2) }}</h4>
    <ul>
        @include('components.sidebarItem', ["url" => route('requesters.index') ,                    "title" => trans_choice('ticket.requester', 1)])
          @include('components.sidebarItem', ["url" => route('teams.index'),      "title" => trans_choice('team.team',        2) ])
            @include('components.sidebarItem', ["url" => route('users.index'),      "title" => trans_choice('ticket.user',      2) ])
            @include('components.sidebarItem', ["url" => route('settings.edit', 1), "title" => trans_choice('setting.setting',  2) ])
            @include('components.sidebarItem', ["url" => route('ticketTypes.index', 1), "title" => trans_choice('ticket.ticketType',  2) ])
            @include('components.sidebarItem', ["url" => route('ticketPostTypes.index', 1), "title" => trans_choice('ticket.ticketPostType',  2) ])
            @include('components.sidebarItem', ["url" => route('ticketCompanies.index', 1), "title" => trans_choice('ticket.ticketCompany',  2) ])
            @include('components.sidebarItem', ["url" => route('categories.index', 1), "title" => trans_choice('ticket.categories',  2) ])
            @include('components.sidebarItem', ["url" => route('channels.index', 1), "title" => trans_choice('ticket.channels',  2) ])

    </ul>
    <br>
    @endif
</div>

<div class="show-mobile absolute ml2 mt3 fs3">
    <span class="fs3 black" onclick="toggleSidebar()">☰</span>
</div>
