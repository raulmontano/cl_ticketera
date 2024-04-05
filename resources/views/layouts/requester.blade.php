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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

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
    @yield('scripts')
</body>
</html>
