@extends('layouts.app')
@section('content')
    <div class="description comment">
        <div class="breadcrumb">
            <a href="{{ url()->previous() }}">{{ trans_choice('ticket.ticket', 2) }}</a>
        </div>
    </div>

    {{ Form::open(["url" => route("tickets.store"),"files" => true]) }}
<div class="comment new-ticket">
      @include('tickets.form.fields')
</div>
    {{ Form::close() }}
@endsection


@push('edit-scripts')

    <script>
      $(document).ready(function(){
          $('form').submit(function(){
              $(this).children('input[type=submit]').prop('disabled', true);
              $(this).find(':button').prop('disabled', true);
          });
      });
    </script>

@endpush
