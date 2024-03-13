@extends('adminlte::page')

@section('title', 'Profil')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Profil</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('drivers') }}">Finde Fahrer</a></li>
            <li class="breadcrumb-item active">Fahrer</li>
        </ol>
    </div>
</div>
@stop

@section('plugins.FullCalendar', true)
@section('plugins.Leaflet', true)

@include('partials.driver-route', ['tabId' => 'route'])
@include('partials.driver-schedule', ['tabId' => 'timetable'])
@include('partials.driver-compare-routes', ['tabId' => 'croutes'])
@include('partials.driver-compare-schedule', ['tabId' => 'ctimetable'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="https://source.boringavatars.com/beam/120/{{ $user->firstname.$user->name }}?colors=546371,E2001A,2B2B2B" alt="User profile picture">
                    </div>

                    <h3 class="profile-username text-center">{{ $user->firstname }} {{ $user->name }}</h3>

                    <p class="text-muted text-center">{{ $user->isDriver ? 'Fahrer' : 'Kein Fahrer' }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Registriert seit</b> <span class="float-right">{{ date('d.m.Y', strtotime($user->created_at)) }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Zuletzt aktualisiert</b> <span class="float-right">{{ date('d.m.Y', strtotime($user->updated_at)) }}</span>
                        </li>
                    </ul>
                    <p id="userMail" class="text-muted text-center mb-0">{{ $user->email }} <button id="copyToClipboard" type="button" class="btn btn-primary btn-sm"><i class="far fa-copy"></i></button></p>
                </div><!-- /.card-body -->
            </div><!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daten</h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-graduation-cap mr-1"></i> Kursname</strong>
                    <p class="text-muted">{{ $user->class }}</p>

                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Ort</strong>
                    <p class="text-muted">{{ $user->city }}</p>

                    <hr>

                    <strong><i class="fas fa-car mr-1"></i> Freie Sitze</strong>
                    <p class="text-muted">{{ $user->freeSeats }}</p>

                    <hr>

                    <strong><i class="far fa-file-alt mr-1"></i> Anmerkungen</strong>
                    <p class="text-muted">{{ $user->notes }}</p>
                </div>
                <!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.col -->
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link {{ $view == 'route' ? 'active' : '' }}" href="#route" data-toggle="tab">Route</a></li>
                        <li class="nav-item"><a class="nav-link {{ $view == 'timetable' ? 'active' : '' }}" href="#timetable" data-toggle="tab">Vorlesungsplan</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ ($view == 'croute' || $view == 'ctimetable') ? 'active' : '' }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Vergleich</a>
                            <div class="dropdown-menu">
                            <a class="dropdown-item {{ $view == 'croute' ? 'active' : '' }}" href="#croutes" data-toggle="tab">Route</a>
                            <a class="dropdown-item {{ $view == 'ctimetable' ? 'active' : '' }}" href="#ctimetable" data-toggle="tab">Vorlesungsplan</a>
                            </div>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane {{ $view == 'route' ? 'active' : '' }}" id="route">
                            @yield('driver-route-html')
                            @yield('driver-route-js')
                        </div><!-- /.tab-pane -->
                        
                        <div class="tab-pane {{ $view == 'timetable' ? 'active' : '' }}" id="timetable">
                            @yield('driver-schedule-html')
                            @yield('driver-schedule-js')
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane {{ $view == 'croute' ? 'active' : '' }}" id="croutes">
                            @yield('driver-compare-routes-html')
                            @yield('driver-compare-routes-js')
                        </div><!-- /.tab-pane -->
                        
                        <div class="tab-pane {{ $view == 'ctimetable' ? 'active' : '' }}" id="ctimetable">
                            @yield('driver-compare-schedule-html')
                            @yield('driver-compare-schedule-js')
                        </div><!-- /.tab-pane -->

                    </div><!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.col -->
    </div> <!-- /.row -->
</div><!-- /.container-fluid -->
@stop

@section('js')
<script>
    document.getElementById('copyToClipboard').addEventListener('click', function() {
        navigator.clipboard.writeText('{{ $user->email }}').then(() => {
            alert("E-Mail kopiert");
        });
    });
</script>
@stop