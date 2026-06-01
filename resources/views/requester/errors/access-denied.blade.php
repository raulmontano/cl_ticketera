@extends('layouts.requester')
@section('content')

    <div class="text-center">

        @if(request('not_logged'))
            <h1 class="mt-4 mb-4"><p>Sesión no iniciada</p></h1>
            <h3>
                <p>Para acceder a esta funcionalidad, debes iniciar sesión previamente en SomosClave. </p>
                <p>Inicia sesión y vuelve a ingresar a esta página.</p>
                <p>
                    <a href="https://somosclave.cl/login.php">
                            somosclave.cl
                        </a>
                </p>
            </h3>
        @else
            <h1 class="mt-4 mb-4">
                <p>No tienes privilegios para acceder a esta funcionalidad</p>
            </h1>
        @endif

    </div>
@endsection