function dhbwLonLan() {
    return [47.6173017, 7.677168658107989]
}

function getCookie(name) {
    let cookieArr = document.cookie.split(';');
    for(let i = 0; i < cookieArr.length; i++) {
        let cookiePair = cookieArr[i].split('=');
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }
    // Return null if not found
    return null;
}

function setDateRangeCookies(startDate, endDate) {
    var now = new Date();
    now.setTime(now.getTime() + 1000*36000); // 10 hours

    document.cookie = "startDate=" + startDate.format('DD.MM.YYYY') + ';expires=' + now.toUTCString() + ';path=/';
    document.cookie = "endDate=" + endDate.format('DD.MM.YYYY') + ';expires=' + now.toUTCString() + ';path=/';
}

function routedMap(id, wayPoints, distCallback=null, timeCallback=null, tabElm=null, tabId=null) {
    const map = L.map(id);

    // Set tile layer
    const osmLayer = L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png').addTo(map);

    // Create a routing control
    var routingControl = L.Routing.control({
        waypoints: wayPoints,
        routeWhileDragging: true,
        geocoder: L.Control.Geocoder.nominatim()
    }).addTo(map);

    var bounds = L.latLngBounds(wayPoints);

    // Adding the class to hide the routing container
    if(distCallback || timeCallback) {
        routingControl.on('routesfound', function(e) {
            var summary = e.routes[0].summary;
            // Displaying distance and time
            if(distCallback) {
                distCallback((summary.totalDistance / 1000).toFixed(2));
            }
    
            if(timeCallback) {
                timeCallback(Math.round(summary.totalTime / 60));
            }
        });
    }

    if(tabElm && tabId) {
        tabElm.on('shown.bs.tab', function(e) {
            if ($(e.target).attr('href') === '#'+tabId) {
                map.invalidateSize(false);
                map.fitBounds(bounds);
            }
        });
    }
    
    return map;
}

function SetCalculation(wrapper) {
    var k = parseFloat($(wrapper + ' p.route-distance').text().match(/\d+\.\d+/)[0]);
    var cpl = $(wrapper + ' input.route-cpl').val();
    var lpk = $(wrapper + ' input.route-lpk').val();
    $(wrapper + ' span.route-cost').text(((lpk/100*k) * cpl).toFixed(2));
}