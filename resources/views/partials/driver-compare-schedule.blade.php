@section('driver-compare-schedule-html')
<!-- Loading Spinner -->
<div id="calendarLoading-comp" class="calendar-loading" style="display: none;">
    <div class="spinner"></div>
</div>

<!-- The calendar -->
<div id="calendar-comp"></div>
@stop

@section('driver-compare-schedule-js')
<script>
    ['DOMContentLoaded', 'contentLoaded'].forEach(event => document.addEventListener(event, function() {
        @if ($view == 'ctimetable')
            InitCalendarComp();
        @else
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#{{ $tabId }}') {
                    if (!window.cCalendarInitialized) {
                        InitCalendarComp();
                        window.cCalendarInitialized = true;
                    }
                }
            });
        @endif
    }));

    function InitCalendarComp(calendarEl) {
        // Show the loading icon
        document.getElementById('calendarLoading-comp').style.display = 'flex';

        var calendarEl = document.getElementById('calendar-comp');

        // Define the URLs for the two event sources
        const url1 = "{{ asset('api/cal') }}/{{ $user->class }}"; // Use the first URL
        const url2 = "{{ asset('api/cal') }}/{{ $cuser->class }}"; // Add the second URL

        // Use Promise.all to fetch both URLs simultaneously
        Promise.all([
            fetch(url1).then(response => response.json()), // Fetch from the first URL
            fetch(url2).then(response => response.json())  // Fetch from the second URL
        ])
        .then(function([data1, data2]) {
            // Process the first dataset
            var events1 = data1.data.map(function(event) {
                return {
                    title: event.summary,
                    start: event.start,
                    end: event.end,
                    description: event.description,
                    color: "#007bff"
                };
            });

            // Process the second dataset
            var events2 = data2.data.map(function(event) {
                return {
                    title: event.summary,
                    start: event.start,
                    end: event.end,
                    description: event.description,
                    color: "#6c757d"
                };
            });

            // Merge the events from both sources
            var allEvents = events1.concat(events2);

            // Initialize FullCalendar with the merged events
            var calendarOptions = {
                initialView: 'timeGridWeek',
                nowIndicator: true,
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                hiddenDays: [0, 6],
                height: 'auto',
                validRange: {
                    start: new Date(),
                },
                events: allEvents // Use the fetched events directly
            };

            var calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);
            calendar.render();
            document.getElementById('calendarLoading-comp').style.display = 'none';
        })
        .catch(function(error) {
            // Handle errors from either fetch request
            // Hide the loading icon if an error occurs
            document.getElementById('calendarLoading-comp').style.display = 'none';
            console.error('Error loading calendar events:', error);
        });;
    }
</script>
@stop
