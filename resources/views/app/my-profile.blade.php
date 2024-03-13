@extends('adminlte::page')

<!-- Plugins -->
@section('plugins.FullCalendar', true)
@section('plugins.Leaflet', true)

<!-- Parent Sections -->
@section('title', 'Profil')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Mein Profil</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<form action="{{ route('profile-form') }}" method="post">
@csrf
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Einstellungen</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img class="profile-user-img img-fluid img-circle" src="https://source.boringavatars.com/beam/120/{{ $user->firstname.$user->name }}?colors=546371,E2001A,2B2B2B" alt="User profile picture">
                </div>
                <div class="form-group row required">
                    <label for="firstname" class="col-sm-2 col-form-label">Vorname</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="firstname" placeholder="{{ $user->firstname }}" value="{{ $user->firstname }}" required readonly>
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="name" class="col-sm-2 col-form-label">Nachname</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" placeholder="{{ $user->name }}" value="{{ $user->name }}" required readonly>
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="email" class="col-sm-2 col-form-label">DHBW E-Mail</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" placeholder="{{ $user->email }}" value="{{ $user->email }}" required readonly>
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="class" class="col-sm-2 col-form-label">Kursname</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="class" name="class" placeholder="{{ $user->class }}" value="{{ $user->class }}" required>
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="city" class="col-sm-2 col-form-label">Wohnort</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="city" name="city" placeholder="{{ $user->city }}" value="{{ $user->city }}" required></select>
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="isDriver" class="col-sm-2 col-form-label">Fahrer</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="isDriver" name="isDriver">
                            <option value="1" {{ $user->isDriver ? 'selected' : '' }}>Ja</option>
                            <option value="0" {{ !$user->isDriver ? 'selected' : '' }}>Nein</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row required" id="freeSeatsGroup" style="{{ !$user->isDriver || $user->isDriver == 0 ? 'display:none' : '' }}">
                    <label for="freeSeats" class="col-sm-2 col-form-label">Freie Sitze</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="freeSeats" name="freeSeats" placeholder="{{ $user->freeSeats }}" value="{{ $user->freeSeats }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="notes" class="col-sm-2 col-form-label">Anmerkungen</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="notes" name="notes" placeholder="{{ $user->notes }}">{{ $user->notes }}</textarea>
                    </div>
                </div>

                <input type="submit" value="Änderungen speichern" class="btn btn-primary float-right">

                <div class="clearfix"></div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#route" data-toggle="tab">Deine Route</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timetable" data-toggle="tab">Dein Vorlesungsplan</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active map-wrapper" id="route">
                        <h4>Route</h4>

                        <p class="text-muted">Die kürzeste Route vom deinem Wohnort zur DHBW.</p>
                        <strong><i class="fas fa-route mr-1"></i> Route</strong>
                        <div id="map" style="height: 400px"></div>

                        <hr>

                        <strong><i class="fas fa-road mr-1"></i> Fahrtweg</strong>
                        <p class="text-muted route-distance"></p>

                        <hr>

                        <strong><i class="fas fa-stopwatch mr-1"></i> Zeit</strong>
                        <p class="text-muted route-time mb-0"></p>

                        <hr>

                        <strong><i class="fas fa-money-bill mr-1"></i> Kosten</strong><br>
                        <input type="number" step="0.1" value="7.5" class="route-lpk form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
                        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">l / 100 km</span>
                        <input type="number" step="0.01" value="1.78" class="route-cpl form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
                        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">€ / l</span>
                        
                        <span class="d-inline-block mr-2" style="vertical-align: middle;">ergibt pro Strecke</span>
                        <span class="route-cost d-inline-block ml-2" style="vertical-align: middle; font-weight: bold">12</span>
                        <span class="d-inline-block" style="vertical-align: middle; font-weight: bold">€</span>
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane" id="timetable">
                        <div id="calendar" style="min-height:200px"></div>
                    </div><!-- /.tab-pane -->
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@stop

@section('js')
<script>
    $(document).ready(function() {

        // 1. Is Driver Select
        $('#isDriver').change(function() {
            if ($(this).val() === '1') {
                $('#freeSeatsGroup').show();
            } else {
                $('#freeSeatsGroup').hide();
            }
        });

        // 2. City Select
        $('#city').select2({
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

        // Define default option
        var defaultValueId = '{{ $user->city }}|{{ $user->city_short }}|{{ $user->cityLat }}|{{ $user->cityLon }}';
        var defaultValueText = '{{ $user->city }}';

        // Create a new option and prepend it to the Select2
        var newOption = new Option(defaultValueText, defaultValueId, true, true);
        $("#city").append(newOption).trigger('change');

        // Make Select2 display this option as selected
        $("#city").trigger({
            type: 'select2:select',
            params: {
                data: { id: defaultValueId, text: defaultValueText }
            }
        });

        // 3. Map
        routedMap(
            'map',
            [[{{ $user->cityLat }}, {{ $user->cityLon }}], dhbwLonLan()],
            function(dist) {
                $('.map-wrapper p.route-distance').html(dist + ' km');
                SetCalculation('.map-wrapper');
            },
            function(time) {
                $('.map-wrapper p.route-time').html(time + ' min');
            },
            $('a[data-toggle="tab"]'),
            'route'
        );

        $('.map-wrapper input.route-cpl, .map-wrapper input.route-lpk').change(function() {
            SetCalculation('.map-wrapper');
        });

        // 4. Calendar
        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'timeGridWeek', // Week view
            nowIndicator: true,
            slotMinTime: '08:00:00', // Start time of the time grid
            slotMaxTime: '20:00:00', // End time of the time grid
            hiddenDays: [0, 6], // Hide Sundays and Saturdays
            height: 'auto',
            validRange: {
                start: new Date(),
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch("{{ asset('api/cal') }}/{{ $user->class }}")
                .then(function(response) {
                    return response.json();
                })
                .then(function(jsonData) {
                    var events = jsonData.data.map(function(event) {
                        return {
                            title: event.summary,
                            start: event.start,
                            end: event.end,
                            description: event.description
                        };
                    });
                    successCallback(events);
                })
                .catch(function(error) {
                    failureCallback(error);
                });
            }
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(e.target).attr('href') === '#timetable') {
                calendar.render();
            }
        });

    });
</script>
@stop
