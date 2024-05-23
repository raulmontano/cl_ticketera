<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,500" rel="stylesheet">
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style_dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">

      <div class="navbar-fixed">
          <nav class="nav-extended white darken-2">
            <div class="center text-center" style="max-width:300px">
                <div class="nav-wrapper">
                  <img src="{{url("images/logo.png")}}" class="w70">
                </div>
              </div>
              <!--        segunda linea de menus-->
              <div class="nav-content">
                <div class="center text-center tabs tabs-transparent grey darken-4">
                  SOLICITUD DE CONTENIDO
                </div>
              </div>

          </nav>
      </div>

        <div class="center w80">
            @include('components.errors')
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    @yield('scripts')

    <script>
    $( function() {
      $( document ).tooltip();
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
