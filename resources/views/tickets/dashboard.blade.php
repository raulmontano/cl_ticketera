@extends('layouts.app')
@section('content')
    
      <div class="row">
      <iframe class="col s12" style="height: 1800px; margin-top:50px; border:none;" src="{{ ENV('DASHBOARD_URL','https://gc.somosclave.cl/custom_dashboard') }} "></iframe>
      </div>
    
@endsection

