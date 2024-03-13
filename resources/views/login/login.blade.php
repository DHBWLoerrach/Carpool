@extends('adminlte::auth.login')

@section('auth_body')

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" placeholder="E-Mail">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @if ($errors->has('email'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('email') }}
            </div>
        @endif
    </div>

    <div class="input-group mb-3">
        <input type="text" name="firstname" class="form-control  {{ $errors->has('firstname') ? 'is-invalid' : '' }}" value="{{ old('firstname') }}" placeholder="Vorname">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
        @if ($errors->has('firstname'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('firstname') }}
            </div>
        @endif
    </div>

    <div class="input-group mb-3">
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Nachname">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
        @if ($errors->has('name'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('name') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary btn-block" id="btn-login">Login</button>
</form>
@endsection

@section('auth_footer')
@endsection
