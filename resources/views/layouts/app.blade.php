<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Handesk') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style_dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script type="text/javascript">
      var lang= '{{ app()->getLocale() }}';
    </script>
</head>
<body>
    <div id="app">
        <div id="popup" class="popup">
            <div id="popupContent"></div>
        </div>
{{--        @include('layouts.header')--}}
        @include('layouts.tinyHeader')
        @include('layouts.sidebar')
        <div class="content" @if(isset($resourceName)) id="content_{{ $resourceName }}" @endif>
            @include('components.errors')
            @yield('content')
        </div>
        {{--@include('layouts.footer')--}}
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    @yield('scripts')
    @stack('edit-scripts')

    <script>
    $( function() {
      $( document ).tooltip();
    } );
    </script>

</body>
</html>
