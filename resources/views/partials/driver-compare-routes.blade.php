
@section('driver-compare-routes-html')
<div class="row">
    <div class="col-lg-6 col-md-12 map-org-wrapper">
        <h4>Original Route</h4>
        <p class="text-muted">Die kürzeste Route vom Wohnort des Fahrers zur DHBW.</p>
        <strong><i class="fas fa-route mr-1"></i> Route</strong>
        <div id="map-org" style="height: 400px"></div>

        <hr>

        <strong><i class="fas fa-road mr-1"></i> Fahrtweg</strong>
        <p class="text-muted route-distance"></p>

        <hr>

        <strong><i class="fas fa-stopwatch mr-1"></i> Zeit</strong>
        <p class="text-muted route-time"></p>

        <hr>

        <strong><i class="fas fa-money-bill mr-1"></i> Kosten</strong><br>
        <input type="number" step="0.1" value="7.5" class="route-lpk form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">l / 100 km</span>
        <input type="number" step="0.01" value="1.78" class="route-cpl form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">€ / l</span>
        <br>
        <span class="d-inline-block mr-2" style="vertical-align: middle;">ergibt pro Strecke</span>
        <span class="route-cost d-inline-block ml-2" style="vertical-align: middle; font-weight: bold">12</span>
        <span class="d-inline-block" style="vertical-align: middle; font-weight: bold">€</span>
    </div>
    <div class="col-lg-6 col-md-12 map-alt-wrapper">
        <h4>Neue Route</h4>
        <p class="text-muted">Die Route einschließlich des Umwegs zu deinem Wohnort.</p>
        <strong><i class="fas fa-route mr-1"></i> Route</strong>
        <div id="map-alt" style="height: 400px"></div>

        <hr>

        <strong><i class="fas fa-road mr-1"></i> Fahrtweg</strong>
        <p class="text-muted route-distance"></p>

        <hr>

        <strong><i class="fas fa-stopwatch mr-1"></i> Zeit</strong>
        <p class="text-muted route-time"></p>

        <hr>

        <strong><i class="fas fa-money-bill mr-1"></i> Kosten</strong><br>
        <input type="number" step="0.1" value="7.5" class="route-lpk form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">l / 100 km</span>
        <input type="number" step="0.01" value="1.78" class="route-cpl form-control d-inline-block mb-0 p-0" style="width: 45px; vertical-align: middle;" />
        <span class="d-inline-block mr-2" style="vertical-align: middle; font-weight: bold">€ / l</span>
        
        <span class="d-inline-block mr-2" style="vertical-align: middle;">ergibt pro Strecke</span>
        <span class="route-cost d-inline-block ml-2" style="vertical-align: middle; font-weight: bold">12</span>
        <span class="d-inline-block" style="vertical-align: middle; font-weight: bold">€</span>
    </div>
</div>
@stop

@section('driver-compare-routes-js')
<script>
    ['DOMContentLoaded', 'contentLoaded'].forEach(event => document.addEventListener(event, function() {
        routedMap(
            'map-org',
            [[{{ $user->cityLat }}, {{ $user->cityLon }}], dhbwLonLan()],
            function(dist) {
                $('.map-org-wrapper p.route-distance').html(dist + ' km');
                SetCalculation('.map-org-wrapper');
            },
            function(time) {
                $('.map-org-wrapper p.route-time').html(time + ' min');
            },
            $('a[data-toggle="tab"]'),
            '{{ $tabId }}'
        );

        $('.map-org-wrapper input.route-cpl, .map-org-wrapper input.route-lpk').change(function() {
            SetCalculation('.map-org-wrapper');
        });

        routedMap(
            'map-alt',
            [[{{ $user->cityLat }}, {{ $user->cityLon }}], [{{ $cuser->cityLat }}, {{ $cuser->cityLon }}], dhbwLonLan()],
            function(dist) {
                $('.map-alt-wrapper p.route-distance').html(dist + ' km');
                SetCalculation('.map-alt-wrapper');
            },
            function(time) {
                $('.map-alt-wrapper p.route-time').html(time + ' min');
            },
            $('a[data-toggle="tab"]'),
            '{{ $tabId }}'
        );

        $('.map-alt-wrapper input.route-cpk, .map-alt-wrapper input.route-lpk').change(function() {
            SetCalculation('.map-alt-wrapper');
        });
    }));
</script>
@stop