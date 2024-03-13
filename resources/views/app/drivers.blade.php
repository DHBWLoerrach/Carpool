@extends('adminlte::page')

@section('title', 'Fahrer finden')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Finde Fahrer</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Finde Fahrer</li>
        </ol>
    </div>
</div>
@stop

@section('plugins.FullCalendar', true)
@section('plugins.Leaflet', true)
@section('plugins.DateRangePicker', true)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-drivers">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $view == 'table' ? 'active' : '' }}" href="{{ route('drivers', ['view' => 'table']) }}">Tabelle</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $view == 'map' ? 'active' : '' }}" href="{{ route('drivers', ['view' => 'map']) }}">Karte</a>
                        </li>
                    </ul>
                    <div class="range-select">
                        @php
                            $cookieStartDate = isset($_COOKIE['startDate']) ? "'".$_COOKIE['startDate']."'" : "moment().startOf('quarter')";
                            $cookieEndDate = isset($_COOKIE['endDate']) ? "'".$_COOKIE['endDate']."'" : "moment().endOf('quarter')";
                            $config = [
                                "showDropdowns" => true,
                                "startDate" => "js:".$cookieStartDate,
                                "endDate" => "js:".$cookieEndDate,
                                "locale" => ["format" => "DD.MM.YYYY"],
                                "minDate" => "js:moment()",
                                "ranges" => [
                                    "Heute" => ["js:moment()", "js:moment()"],
                                    "Morgen" => ["js:moment().add(1, 'days')", "js:moment().add(1, 'days')"],
                                    "Diese Woche" => ["js:moment()", "js:moment().endOf('week')"],
                                    "Diesen Monat" => ["js:moment()", "js:moment().endOf('month')"],
                                    "Dieses Quartal" => ["js:moment()", "js:moment().endOf('quarter')"],
                                    "Nächstes Quartal" => ["js:moment().add(1, 'quarter').startOf('quarter')", "js:moment().add(1, 'quarter').endOf('quarter')"],
                                ],
                            ];
                        @endphp
                        <x-adminlte-date-range name="drCustomRanges" label="Zeitraum" igroup-size="sm" :config="$config">
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-date-range>
                    </div>
                </div>
                <div class="card-body table-responsiv">
                    @if($view == 'table')
                        <table id="driversTable" class="table table-bordered table-striped table-drivers display responsive nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Kurs</th>
                                    <th>Ort</th>
                                    <th>Passende Tage</th>
                                    <th>Optionen</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Kurs</th>
                                    <th>Ort</th>
                                    <th>Passende Tage</th>
                                    <th>Optionen</th>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Modal: Matching Days -->
                        <div class="modal fade" id="matchModal" tabindex="-1" role="dialog" aria-labelledby="matchModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="matchModalLabel">Passende Tage</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body overflow-auto">
                                        <ul class="list-group" id="mDayList">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($view == 'map')
                        <div id="drivers-map" style="height: 400px"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    @if($view == 'table')
        <script>
            $(document).ready(function() {

                // 1. Data Table
                var table = $('#driversTable').DataTable({
                    responsive: true,
                    stateSave: true,
                    language: {
                        url: 'assets/json/dataTables.german.json',
                    },
                    drawCallback: function() {
                        // enable tool tips
                        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        let tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)); 
                    },
                    ajax: "api/drivers",
                    columns: [
                        // Specify the data for each column
                        { "data": "id" },
                        { "data": "name" },
                        { "data": "class" },
                        { "data": "city" },
                        { "data": "matching_days" },
                        { "data": "options" },
                    ],
                    columnDefs: [
                        {
                            "targets": [ -1 ],
                            "searchable": false,
                            "orderable": false,
                            "width": "2%",
                            "data": "options",
                            "defaultContent": "",
                            "render": function(data, type, row, meta) {
                                let ID = row["id"];

                                let last_col = '<div class="btn-group">';
                                last_col += '<a class="btn bg-gradient-primary btn-profile btn-sm" data-bs-toggle="tooltip" title="Profil anzeigen" href="{{ route("drivers") }}/'+ID+'"><i class="fas fa-address-card"></i></a>';
                                last_col += '<a class="btn bg-gradient-secondary btn-route btn-sm" data-bs-toggle="tooltip" title="Fahrstrecke vergleichen" href="{{ route("drivers") }}/'+ID+'?view=croute"><i class="fas fa-route"></i></a>';
                                last_col += '<a class="btn bg-gradient-secondary btn-time btn-sm" data-bs-toggle="tooltip" title="Vorlesungsplan vergleichen" href="{{ route("drivers") }}/'+ID+'?view=ctimetable"><i class="fas fa-calendar-alt"></i></a>';
                                last_col += '</div>';

                                return last_col;
                            }
                        },
                        {
                            "targets": [ -2 ],
                            "type": "span-value",
                            "render": function(data, type, row, meta) {
                                let ID = row['id'];
                                let percent = (data[0]/data[1]*100)==0?1:(data[0]/data[1]*100);
                                let color = (percent>=60?'bg-success':(percent>=30?'bg-warning':'bg-danger'));
                                let last_col = '<button type="button" class="popover-button" id="'+ID+'"><div class="progress progress-xs"><div class="progress-bar '+color+'" style="width: '+percent+'%"></div></div><span class="badge '+color+'">'+data[0]+' / '+data[1]+'</span></button>';
                                return last_col;
                            }
                        }
                    ],
                });

                @if(!empty($q))
                table.search("{{ $q }}").draw();
                @endif

                // Set order functionality for Matching Days column
                $.fn.dataTable.ext.type.order['span-value-pre'] = function(data) {
                    // Extract the numerical value from the span
                    var matches = data.match(/<span class="badge bg-\w+">(\d+) \/ \d+<\/span>/);
                    return matches ? parseInt(matches[1], 10) : 0;
                };

                // 2. Matching Days Modal
                $('#driversTable').on('click', '.popover-button', function() {
                    // Retrieve startDate and endDate from cookies without using a library
                    let startDate = getCookie('startDate');
                    let endDate = getCookie('endDate');

                    // Prepare the data object for AJAX request including startDate and endDate if they exist
                    let dataObj = {};
                    if (startDate !== null) dataObj.start = startDate;
                    if (endDate !== null) dataObj.end = endDate;

                    // AJAX request to /api/md/{ID} to get all matching days
                    $.ajax({
                        url: 'api/md/'+$(this).attr('id'),
                        type: 'GET',
                        data: dataObj,
                        success: function(data) {
                            let html = '';
                            if(data['result'].length == 0) {
                                html = '<li class="list-group-item">Keine passenden Tage gefunden</li>';
                            } else {
                                data['result'].forEach(function(item) {
                                    let day = new Date(item);
                                    html += '<li class="list-group-item">'+day.getDate()+'. '+day.toLocaleString('de-DE', { month: 'long' })+' '+day.getFullYear()+'</li>';
                                });
                            }
                            $('#mDayList').html(html);
                        },
                        error: function(error) {
                            // Handle error
                            $('#mDayList').html('<li class="list-group-item">'+error['responseJSON']['error']+'</li>');
                        }
                    });

                    $('#matchModal').modal('show');
                });

                // 3. Date Range Selection
                $('input[name="drCustomRanges"]').on('apply.daterangepicker', function(ev, picker) {
                    setDateRangeCookies(picker.startDate, picker.endDate);
                    table.ajax.reload();
                });
            });
        </script>
    @elseif($view == 'map')
        <script>
            $(document).ready(function() {
                const map = L.map('drivers-map').setView([47.616717, 7.678283], 8); // Initial map view

                // Tile layer
                const osmLayer = L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png').addTo(map);

                const locations = [
                    @foreach ($users as $user)
                        { lat: {{ $user->cityLat }}, lng: {{ $user->cityLon }}, id: '{{ $user->id }}', user: '{{ $user->firstname . ' ' . $user->name }}', class: '{{ $user->class }}', md: '{{ $mds[$user->id] }}' },
                    @endforeach
                ];

                var markers = L.markerClusterGroup();
                map.addLayer(markers);

                // Object to hold markers by their position
                let markersByPosition = {};

                locations.forEach(location => {
                    // Create a unique key for the position
                    const positionKey = `${location.lat},${location.lng}`;

                    // Initialize the array for this position if it doesn't exist
                    if (!markersByPosition[positionKey]) {
                        markersByPosition[positionKey] = [];
                    }

                    // Create the marker
                    const marker = L.marker([location.lat, location.lng]).bindTooltip(location.user+'<br>'+location.class+'<br>Passend: '+location.md, {className: "my-label", offset: [0, 0] });

                    // Store the marker along with its redirect URL
                    markersByPosition[positionKey].push({
                        marker: marker,
                        redirectUrl: 'drivers/' + location.id
                    });

                    // Add the marker to the clustering group instead of directly to the map
                    markers.addLayer(marker);
                });

                // Attach click event to cycle through markers at the same position if there are multiple
                Object.values(markersByPosition).forEach(markerGroup => {
                    if (markerGroup.length > 1) {
                        markerGroup.forEach((item, index) => {
                            item.marker.on('click', function() {
                                // Cycle through markers' URLs at this position
                                window.location.href = markerGroup[index].redirectUrl;
                            });
                        });
                    } else {
                        // If there's only one marker, just redirect on click
                        markerGroup[0].marker.on('click', function() {
                            window.location.href = markerGroup[0].redirectUrl;
                        });
                    }
                });

                // Date Range Selection
                $('input[name="drCustomRanges"]').on('apply.daterangepicker', function(ev, picker) {
                    setDateRangeCookies(picker.startDate, picker.endDate);
                    window.location.reload();
                });

            });
        </script>
    @endif
@stop
