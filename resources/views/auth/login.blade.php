@extends('auth.layout')

@section('content')
    <div class="center text-center mt5" style="max-width:400px">
            <img src="{{url("images/logo.png")}}" class="w80">

            <form class="form-horizontal mt-2" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <input id="email" type="email" placeholder="Email" class="w80 form-control" name="email" value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <br>
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <input id="password" type="password" placeholder="Contraseña" class="w80 form-control" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="mh3 mb2">
                    <button type="submit" class="btn btn-primary ph5 w80">Iniciar sesión</button>
                </div>
                <div class="mb3">
                    <input type="checkbox" class="" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Recordar usuario
                </div>

                <div>
                    <a class="btn btn-link" href="{{ route('password.request') }}"> ¿Olvidaste tu contraseña? </a>
                </div>
            </form>

    </div>
@endsection
