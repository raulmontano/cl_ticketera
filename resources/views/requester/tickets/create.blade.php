@extends('layouts.requester')
@section('content')

    {{ Form::open(["url" => route("requester.tickets.store"),"files" => true]) }}
<div class="comment new-ticket">

    <div class="form-row justify-content-md-center">

      <div class="form-group col-md-3">
          <label for="requester_name">{{trans_choice('ticket.requester',2) }}</label>
          <input type="name" id="requester_name" name="requester[name]" class="form-control" required value="{{ old('requester.name') ? old('requester.name') : (isset($name) ? $name : '' )}}"/>
      </div>

      <div class="form-group col-md-3">
          <label for="requester_email">{{ __('user.email') }}</label>
          <input type="email" id="requester_email" name="requester[email]" class="form-control" required value="{{ old('requester.email') ? old('requester.email') : (isset($email) ? $email : '' )}}"/>
      </div>

    </div>

    @include('tickets.form.fields');

</div>
    {{ Form::close() }}
@endsection
