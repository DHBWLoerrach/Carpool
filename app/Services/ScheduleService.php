<?php

namespace App\Services;

use \App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use ICal\ICal;
use DateTime;
use Illuminate\Support\Facades\Storage;

class ScheduleService
{
    /**
     * Create list with filtered events from ICS content
     * 
     * @param string $responseBody
     * @return array
     */
    private function filterEvents($responseBody)
    {
        $scheduleByDay = [];

        try {
            $ical = new ICal();
            $ical->initString($responseBody);
    
            foreach ($ical->events() as $event) {
                $eventStart = new DateTime($event->dtstart);
                $eventEnd = new DateTime($event->dtend);
                $eventDate = $eventStart->format('Y-m-d');
    
                // Exclude past events
                if ($eventDate >= date('Y-m-d')) {
                    // Exclude all day events
                    if (strpos($event->dtstart, 'T') && ($event->dtstart != $event->dtend)) {
                        // Exclude online events
                        if (!empty($event->location) && !strpos($event->location, 'bbb.dhbw') && !preg_match('/online/i', $event->location)) {

                            if (!isset($scheduleByDay[$eventDate])) {
                                // Add new day to array if it does not exist
                                $scheduleByDay[$eventDate] = [
                                    'start_time' => $eventStart,
                                    'end_time' => $eventEnd,
                                    'events' => []
                                ];
                            } else {
                                // Update start and end time of existing day
                                if ($eventStart < $scheduleByDay[$eventDate]['start_time']) {
                                    $scheduleByDay[$eventDate]['start_time'] = $eventStart;
                                }
                                if ($eventEnd > $scheduleByDay[$eventDate]['end_time']) {
                                    $scheduleByDay[$eventDate]['end_time'] = $eventEnd;
                                }
                            }

                            // Add current event to day
                            $scheduleByDay[$eventDate]['events'][] = [
                                'start' => $event->dtstart,
                                'end' => $event->dtend,
                                'summary' => $event->summary,
                                'description' => $event->description
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            report($e);
            return null;
        }

        return $scheduleByDay;
    }

    /**
     * Add/update class schedule
     * 
     * @param string $class
     * @param bool $skipTimer
     * @param bool $skipTimerIfFailed
     * @return int
     */
    public function addOrUpdate($class, $skipTimer=false, $skipTimerIfFailed=false)
    {
        // Clean up: Delete all past schedule entries and not current schedules
        DB::table('day_schedules')->where('day', '<', date('Y-m-d'))->delete();
        DB::table('schedules')->whereNotIn('class', json_decode(Storage::get('classes.json'))->courseList)->delete(); // TODO: Check if this works

        // If schedule does not exist, create it
        $schedule = DB::table('schedules')->where('class', $class)->first();
        if(!$schedule) {
            $scheduleId = DB::table('schedules')->insertGetId(['class' => $class, 'hash' => null, 'lastUpdated' => null]);
            $schedule = DB::table('schedules')->where('id', $scheduleId)->first();
        }

        $scheduleId = $schedule->id;
        $scheduleHash = $schedule->hash;
        
        // Only update, if older than 5 days or exceptions apply (skipTimer, skipTimerIfFailed)
        if($skipTimer || ($skipTimerIfFailed && !$scheduleHash) || strtotime($schedule->lastUpdated) < strtotime('-5 days')) {
            // Get ICS calender from URL and parse it
            $response = Http::get('https://webmail.dhbw-loerrach.de/owa/calendar/kal-'.$class.'@dhbw-loerrach.de/Kalender/calendar.ics');
            $responseBody = $response->body();

            if($response->status() == 200) {
                // Only update, if calendar was changed
                if(md5($responseBody) != $scheduleHash) {
                    // Parse ICS and extract filtered events
                    $scheduleByDay = $this->filterEvents($responseBody);
                    if($scheduleByDay !== null) {
                        // Set hash to new value
                        $scheduleHash = md5($responseBody);
                        // Delete old entries
                        DB::table('day_schedules')->where('schedule_id', $scheduleId)->delete();
                        // Set new entries
                        foreach ($scheduleByDay as $day => $times) {
                            DB::table('day_schedules')->insert([
                                'schedule_id' => $scheduleId,
                                'day' => $day,
                                'start_time' => $times['start_time']->format('H:i:s'),
                                'end_time' => $times['end_time']->format('H:i:s'),
                                'json' => json_encode($times['events'])
                            ]);
                        }
                    }
                }
            }

            // Update the lastUpdated and hash value
            DB::table('schedules')->where('class', $class)->update(['lastUpdated' => date('Y-m-d H:i:s'), 'hash' => $scheduleHash]);
        }

        // If correct data is present (hash is not null), return scheduleId, else return false
        return $scheduleHash ? $scheduleId : false;
    }

    /**
     * Retrieves the matching days between userA and userB within the given time frame
     *
     * @param User $userA The user to compare with
     * @param User $userB The user to compare with
     * @param string $start The start date
     * @param string $end The end date
     */    
    public function getMatchingDays($userA, $userB, $start, $end)
    {
        // Update cache of user and matched user
        $classA = $this->addOrUpdate($userA->class);     // Self
        $classB = $this->addOrUpdate($userB->class);     // Driver

        if($classA && $classB) {
            $matchingDays = DB::table('day_schedules as A')
            ->join('day_schedules as B', function($join) use ($classA, $classB) {
                $join->on('A.day', '=', 'B.day')
                     ->where('A.schedule_id', $classA)
                     ->where('B.schedule_id', $classB);
            })
            ->whereRaw('TIMESTAMPDIFF(MINUTE, A.start_time, B.start_time) BETWEEN -60 AND 30')      // negative value means B (driver) starts earlier, positive value means A (self) starts earlier
            ->whereRaw('TIMESTAMPDIFF(MINUTE, A.end_time, B.end_time) BETWEEN -30 AND 60')          // negative value means B (driver) ends earlier, positive value means A (self) ends earlier
            ->whereBetween('A.day', [$start, $end])
            ->select('A.day')
            ->groupBy('A.day')
            ->get()
            ->pluck('day');

            return $matchingDays;
        }

        return false;
    }

    /**
     * Retrieves the total days of the users schedule within the given time frame
     *
     * @param User $user The user to check
     * @param string $start The start date
     * @param string $end The end date
     */ 
    public function getTotalDays($user, $start, $end)
    {
        // Update cache of user
        $class = $this->addOrUpdate($user->class);     // Self

        if($class) {
            $totalDays = DB::table('day_schedules')
            ->where('schedule_id', $class)
            ->whereBetween('day', [$start, $end])
            ->count();

            return $totalDays;
        }

        return -1;
    }

    /**
     * Convert date format from dd.mm.yyyy to yyyy-mm-dd
     * 
     * @param string $date
     * @return string|bool
     */
    public function convertDateFormat($date)
    {
        $parts = explode('.', $date);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return false;
    }

    /**
     * Retrieve start and end date from cookies
     * 
     * @return array
     */
    public function retrieveStartAndEnd()
    {
        // Get cookies and convert dates from dd.mm.yyyy to yyyy-mm-dd
        $startFormatted = $this->convertDateFormat($_COOKIE['startDate'] ?? null);
        $endFormatted = $this->convertDateFormat($_COOKIE['endDate'] ?? null);

        // Return converted dates if valid, else return default dates
        $start = ($startFormatted && strtotime($startFormatted)) ? $startFormatted : date('Y-m-d');
        $end = ($endFormatted && strtotime($endFormatted)) ? $endFormatted : date('Y-m-d', strtotime('+3 months', strtotime($start)));
        return [$start, $end];
    }
}