@extends('adminlte::auth.register')

@section('auth_body')
<form action="{{ route('register-form') }}" method="post">
    @csrf
    <!-- First Name -->
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Vorname" name="firstname" value="{{ $user->firstname }}" required readonly>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
    </div>

    <!-- Last Name -->
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Nachname" name="name" value="{{ $user->name }}" required readonly>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
    </div>

    <!-- DHBW Email -->
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="DHBW E-Mail" name="email" value="{{ $user->email }}" required readonly>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>

    <!-- Class -->
    <div class="input-group mb-3">
        <select id="class" class="form-control {{ $errors->has('class') ? 'is-invalid' : '' }}" name="class" value="{{ old('class') }}">
            <option></option>
            @foreach($classes as $class)
                <option value="{{ $class }}">{{ $class }}</option>
            @endforeach
        </select>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-graduation-cap"></span>
            </div>
        </div>
        @if ($errors->has('class'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('class') }}
            </div>
        @endif
    </div>

    <!-- City Search Drowdown -->
    <div class="input-group mb-3">
        <select id="city" class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" name="city" value="{{ old('city') }}"></select>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-home"></span>
            </div>
        </div>
        @if ($errors->has('city'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('city') }}
            </div>
        @endif
    </div>

    <!-- Is Driver Dropdown -->
    <div class="input-group mb-3">
        <select class="form-control {{ $errors->has('isDriver') ? 'is-invalid' : '' }}" name="isDriver" id="isDriver">
            <option value="0" {{ old('isDriver') == 0 ? "selected" : "" }}>Ich bin kein Fahrer</option>
            <option value="1" {{ old('isDriver') == 1 ? "selected" : "" }}>Ich bin ein Fahrer</option>
        </select>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-car"></span>
            </div>
        </div>
        @if ($errors->has('isDriver'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('isDriver') }}
            </div>
        @endif
    </div>

    <!-- Free Car Seats -->
    <div class="input-group mb-3" id="freeSeatsGroup" style="{{ !old('isDriver') || old('isDriver') == 0 ? 'display:none' : '' }}">
        <input type="number" class="form-control {{ $errors->has('freeSeats') ? 'is-invalid' : '' }}" placeholder="Freie Sitze" name="freeSeats" value="{{ old('freeSeats') }}">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-car"></span>
            </div>
        </div>
        @if ($errors->has('freeSeats'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('freeSeats') }}
            </div>
        @endif
    </div>

    <!-- Notes/Description -->
    <div class="input-group mb-3">
        <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" placeholder="Anmerkungen" name="notes" value="{{ old('notes') }}"></textarea>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-pencil-alt"></span>
            </div>
        </div>
        @if ($errors->has('notes'))
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $errors->first('notes') }}
            </div>
        @endif
    </div>

    <!-- Terms -->
    <div class="icheck-primary">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">
            Ich stimme den <a href="#">Regeln</a> zu
        </label>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Registrieren</button>
</form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('logout') }}">
            Zurück zum Login
        </a>
    </p>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#city').select2({
            placeholder: "Wähle deine Stadt",
            ajax: {
                url: 'https://nominatim.openstreetmap.org/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        format: 'json',
                        limit: 10
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.display_name,
                                id: item.display_name  + '|' + item.name  + '|' + item.lat + '|' + item.lon
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        }).on('select2:open', () => {
            // After the dropdown has opened, immediately focus the search field
            document.querySelector('.select2-search__field').focus();
        });

        $('#class').select2({
            placeholder: "Wähle deinen Kurs",
            allowClear: true
        }).on('select2:open', () => {
            // After the dropdown has opened, immediately focus the search field
            document.querySelector('.select2-search__field').focus();
        });;

        $('#isDriver').change(function() {
            if ($(this).val() === '1') {
                $('#freeSeatsGroup').show();
            } else {
                $('#freeSeatsGroup').hide();
            }
        });
    });
</script>
@stop