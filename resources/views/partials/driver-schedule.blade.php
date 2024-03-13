@section('driver-schedule-html')
<!-- Loading Spinner -->
<div id="calendarLoading" class="calendar-loading" style="display: none;">
    <div class="spinner"></div>
</div>

<!-- The calendar -->
<div id="calendar"></div>
@stop

@section('driver-schedule-js')
<script>
    ['DOMContentLoaded', 'contentLoaded'].forEach(event => document.addEventListener(event, function() {
        @if ($view == 'timetable')
            InitCalendar();
        @else
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#{{ $tabId }}') {
                    if (!window.CalendarInitialized) {
                        InitCalendar();
                        window.CalendarInitialized = true;
                    }
                }
            });
        @endif
    }));

    function InitCalendar() {
        // Show the loading icon
        document.getElementById('calendarLoading').style.display = 'flex';

        var calendarEl = document.getElementById('calendar');

        fetch("{{ asset('api/cal') }}/{{ $user->class }}").then(response => response.json())
        .then(function(jsonData) {
            // Process the first dataset
            var events = jsonData.data.map(function(event) {
                return {
                    title: event.summary,
                    start: event.start,
                    end: event.end,
                    description: event.description,
                    color: "#007bff"
                };
            });

            // Initialize FullCalendar with the events
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
                events: events // Use the fetched events directly
            };

            var calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);
            calendar.render();
            document.getElementById('calendarLoading').style.display = 'none';
        })
        .catch(function(error) {
            // Handle errors from either fetch request
            // Hide the loading icon if an error occurs
            document.getElementById('calendarLoading').style.display = 'none';
            console.error('Error loading calendar events:', error);
        });;
    }
</script>
@stop
