@extends('adminlte::page')

@section('title', 'Carpooling Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $user_count }}</h3>
                    <p>Registrierte Benutzer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Mehr Info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $driver_count }}</h3>
                    <p>Registrierte Fahrer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-car"></i>
                </div>
                <a href="{{ route('drivers') }}" class="small-box-footer">
                    Mehr Info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $class_count }}</h3>
                    <p>Unterschiedliche Kurse</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Mehr Info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kürzlich angemeldete Fahrer</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">

                        @foreach($users as $user)
                            <li class="item">
                                <div class="product-img">
                                    <img class="profile-user-img img-fluid img-circle" src="https://source.boringavatars.com/beam/120/{{ $user->firstname.''.$user->name }}?colors=546371,E2001A,2B2B2B" alt="User profile picture">
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('driver', ['id' => $user->id]) }}" class="product-title">
                                        {{ $user->firstname.' '.$user->name }}
                                    </a>
                                    <span class="product-description">
                                        {{ $user->city_short }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer text-center">
                    <a style="cursor: pointer;" href="drivers" class="uppercase">Alle Fahrer</a>
                </div>

            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Carpooling Web App</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    Carpooling ist nicht nur eine Möglichkeit, Reisekosten zu teilen, sondern auch ein wichtiger Beitrag zum Umweltschutz.
                    Unsere Carpooling-Web-App für Studierende vereinfacht die Bildung von Fahrgemeinschaften. Sie ermöglicht es, Fahrten zu teilen, Mitfahrgelegenheiten zu suchen oder anzubieten und sich mit anderen Studierenden zu vernetzen.
                    So fördert die App nachhaltige Mobilität, senkt Kosten und unterstützt einen grüneren Campus.
                </div>
                <div class="card-footer text-center">
                    <a href="#" class="uppercase">Mehr Informationen</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Achtung</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body bg-danger">
                    Diese Webseite dient als interner Prototyp und ist ausschließlich zu Demonstrationszwecken gedacht.
                    Die Daten der registrierten Nutzer sind fiktiv und spiegeln keine realen Personen wider. Jegliche Übereinstimmungen mit realen Personen sind rein zufällig und nicht beabsichtigt.
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
@stop

@section('css')
@stop
