<div id="ticket-info" class="">

  <div class="card-text">

    <div class="row container">
        <div class="col-md-6">

          <div class="row">
            <b>{{ __('ticket.reference_number')}}:&nbsp;</b>
              {{  $ticket->reference_number }}
          </div>

          <div class="row">
            <b>{{ __('ticket.type')}}:&nbsp;</b>
              {{  $ticket->type->name }} {{  $ticket->postType->name }} {{  $ticket->company->name }}
          </div>

          <div class="row">
            <b>{{ trans_choice('ticket.channels', 1)}}:&nbsp;</b>
              {{  $ticket->tagsString() }}
          </div>

          <div class="row">
            <b>{{ trans_choice('ticket.categories', 1)}}:&nbsp;</b>
              {{  $ticket->categoriesString() }}
          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <b>{{ __('ticket.priority')}}:&nbsp;</b>
            {{ __("ticket." . $ticket->priorityName() ) }}
          </div>

          <div class="row">
            <b>{{ __('ticket.status')}}:&nbsp;</b>
            {{ __("ticket." . $ticket->statusName() ) }}
          </div>

          <div class="row">
            @if($ticket->start_date)
              {{ __('ticket.start_date')}} {{  $ticket->start_date }}
            @endif

            @if($ticket->end_date)
            {{ __('ticket.end_date')}} {{  $ticket->end_date }}
            @endif
          </div>
        </div>
        <div class="col-md-12">
          @include('components.attachments', ["attachments" => $ticket->attachments])
        </div>
    </div>
  </div>

    {{--<a class="ml4" title="Public Link" href="{{route('requester.tickets.show',$ticket->public_token)}}"> @icon(globe) </a>--}}
</div>

<div id="ticket-edit" class="hidden" class="">
{{ Form::open(["url" => route("tickets.update", $ticket),"files" => true,"method" => "PUT"]) }}

@php

  $channels = $ticket->tags->pluck('name')->toArray();
  $categories = $ticket->categories->pluck('name')->toArray();

  $title = old('title') ? old('title') : $ticket->title;
  $body = old('body') ? old('body') : $ticket->body;

@endphp

<div class="comment new-ticket">

  <div class="form-row justify-content-md-center">
    <div class="form-group col-md-6">

      <div class="form-row">
        <label for="title">{{ __('ticket.subject') }}</label>
        <input name="title" id="title" class="form-control" required value="{{ old('title') ? old('title') : $title}}"/>
      </div>

      <div class="form-row">
        <label for="body">{{ __('ticket.body') }}</label>
        <textarea  name="body" class="w100" required>{{ old('body') ? old('body') : $body }}</textarea>
      </div>

      <div class="form-row">
        @include('components.uploadAttachment', ["type" => "tickets"])
      </div>

    </div>
  </div>

  <div class="form-row justify-content-md-center">
    <div class="form-group col-md-3">

      <div class="form-row mr-2">
        <label for="start_date">{{ __('ticket.start_date') }}</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') ? old('start_date') : $ticket->start_date}}">
      </div>
    </div>

    <div class="form-group col-md-3">

      <div class="form-row">
        <label for="end_date">{{ __('ticket.end_date') }}</label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') ? old('end_date') : $ticket->end_date}}">
      </div>
    </div>
  </div>

  <div class="form-row justify-content-md-center">
      <div class="form-group col-md-3">
        <label for="channels">{{ trans_choice('ticket.channels', 1)}}</label>
        @foreach(\App\Channel::all() as $i => $channel)
        <div class="form-check">
            <input name="channels[]" id="channel_{{ $channel->id }}" type="checkbox" @if(count($channels) && in_array(strtolower($channel->name),$channels)) checked @endif class="form-check-input" value="{{ $channel->name }}" />
             <label class="form-check-label check" for="channel_{{ $channel->id }}">
               {{ $channel->name }}
            </label>

            @if(strtolower($channel->name) == 'call center')
            <span data-toggle="tooltip" data-placement="top" title="Canales Digitales, Sucursal Virtual, Redes Sociales.">
              <i type="button" class="fa fa-info"></i>
            <span>
            @endif
        </div>
        @endforeach
      </div>

      <div class="form-group col-md-3">
        <label for="categories">{{ trans_choice('ticket.categories', 1)}}</label>
        @foreach(\App\Category::all() as $category)
          <div class="form-check">
            <input name="categories[]" class="form-check-input" @if(count($categories) && in_array(strtolower($category->name),$categories)) checked @endif id="category_{{ $category->id }}" type="checkbox" value="{{ $category->name }}" />
            <label class="form-check-label check" for="category_{{ $category->id }}">
              {{ $category->name }}
            </label>
          </div>
        @endforeach
      </div>

      <div class="form-group col-md-3">

        <select name="company" required class="custom-select">
          <option value='' @if(!old('company')) selected @endif>-- {{ trans_choice('ticket.company', 2)}} --</option>
            @foreach(\App\TicketCompany::all() as $company)
                <option value="{{$company->id}}" @if($ticket->company && $company->id == $ticket->company->id) selected @endif>{{ $company->name }}</option>
            @endforeach
        </select>

        <select name="type" required class="custom-select">
          <option value=''>-- {{ trans_choice('ticket.type', 2)}} --</option>
            @foreach(\App\TicketType::all() as $type)
                <option value="{{$type->id}}" @if($ticket->type && $type->id == $ticket->type->id) selected @endif>{{ $type->name }}</option>
            @endforeach
        </select>

        <select name="post_type" required class="custom-select">
          <option value=''>-- {{ trans_choice('ticket.postType', 2)}} --</option>
            @foreach(\App\TicketPostType::all() as $postType)
                <option value="{{$postType->id}}" @if($ticket->postType && $postType->id == $ticket->postType->id) selected @endif>{{ $postType->name }}</option>
            @endforeach
        </select>

        <div class="form-row">
          <label for="priority">{{ __('ticket.priority')}}</label>
          <select name="priority" class="custom-select">
              <option value="{{\App\Ticket::PRIORITY_LOW}}"  @if($ticket->priority == App\Ticket::PRIORITY_LOW) selected @endif         >{{ __("ticket.low") }}</option>
              <!--option value="{{\App\Ticket::PRIORITY_NORMAL}}"  @if($ticket->priority == App\Ticket::PRIORITY_NORMAL) selected @endif   >{{ __("ticket.normal") }}</option-->
              <option value="{{\App\Ticket::PRIORITY_HIGH}}"  @if($ticket->priority == App\Ticket::PRIORITY_HIGH) selected @endif       >{{ __("ticket.high") }}</option>
              <!--option value="{{\App\Ticket::PRIORITY_BLOCKER}}"  @if($ticket->priority == App\Ticket::PRIORITY_BLOCKER) selected @endif >{{ __("ticket.blocker") }}</option-->
          </select>
        </div>

      </div>

    </div>

    <div class="form-row justify-content-md-center">
      <div class="form-group col-md-12">

        <div class="form-row float-right">
          <button class="ph3 ml1 btn btn-primary">{{ __('ticket.update') }}</button>
          <button class="ph3 ml1 btn btn-secondary" onClick="$('#edit-ticket-button').show(); $('#ticket-info').show(); $('#ticket-edit').hide(); return false;">{{ __('ticket.cancel') }}</button>
        </div>

      </div>
    </div>

</div>
{{ Form::close() }}
</div>
