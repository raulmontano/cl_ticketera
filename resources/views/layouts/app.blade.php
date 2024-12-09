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
    <link href="{{ asset('css/style_dashboard.css?05122024.1') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">

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
    <script src="{{ asset('js/app.js?v=05122024') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    @yield('scripts')
    @stack('edit-scripts')

    <script>
    $( function() {
      $( document ).tooltip();

      $("#filtersForm .reset").click(function(event) {
          event.preventDefault();
          $(this).closest('form').find("input[type=text], input[type=date]").val("");

          $(this).closest('form').find("input[type=checkbox]").prop('checked', false);

          return false;
      });

    } );
    </script>

    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':

                    toastr.options.timeOut = 5000;
                    toastr.info("{{ Session::get('message') }}");

                    break;
                case 'success':

                    toastr.options.timeOut = 5000;
                    toastr.success("{{ Session::get('message') }}");

                    break;
                case 'warning':

                    toastr.options.timeOut = 5000;
                    toastr.warning("{{ Session::get('message') }}");


                    break;
                case 'error':

                    toastr.options.timeOut = 5000;
                    toastr.error("{{ Session::get('message') }}");


                    break;
            }
        @endif
    </script>

</body>
</html>
