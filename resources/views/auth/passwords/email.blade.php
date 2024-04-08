@extends('auth.layout')

@section('content')
<div class="container">
        <div class="center text-center mt5" style="max-width:400px">
            <img src="{{url("images/logo.png")}}" class="w80">
                    @if (session('status'))
                        <div class="alert alert-success">
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-12 control-label">Restablecer contraseña</label>

                            <div class="col-md-12">
                                <input id="email" type="email" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Enviar enlace para restablecer contraseña
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
</div>
@endsection
