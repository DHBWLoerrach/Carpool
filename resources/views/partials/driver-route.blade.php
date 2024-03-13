@section('driver-route-html')
<div class="row">
    <div class="col-lg-12 col-md-12 map-wrapper">
        <h4>Route</h4>
        <p class="text-muted">Die kürzeste Route vom deinem Wohnort zur DHBW.</p>
        <strong><i class="fas fa-route mr-1"></i> Route</strong>
        <div id="map" style="height: 400px"></div>

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

@section('driver-route-js')
<script>
    ['DOMContentLoaded', 'contentLoaded'].forEach(event => document.addEventListener(event, function() {
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
            '{{ $tabId }}'
        );

        $('.map-wrapper input.route-cpl, .map-wrapper input.route-lpk').change(function() {
            SetCalculation('.map-wrapper');
        });
    }));
</script>
@stop