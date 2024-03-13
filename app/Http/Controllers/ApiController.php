<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use ICal\ICal;
use DateTime;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * GET: Get all drivers and the relevant data for the DataTable
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gDrivers(Request $request)
    {
        $data = [];

        // Retrieve time frame for matching days
        [$start, $end] = $this->scheduleService->retrieveStartAndEnd();

        // Get currently logged in user
        $user = Auth::user();
        $totalDays = $this->scheduleService->getTotalDays($user, $start, $end);

        // Loop through all users and add to data array
        foreach(User::all() as $u) {
            if($user == $u || !$u->isDriver) continue; // Skip current user and non-drivers

            $matchingDays = $this->scheduleService->getMatchingDays($user, $u, $start, $end);
            $data[] = [
                'id' => $u->id,
                'name' => $u->firstname.' '.$u->name,
                'class' => $u->class,
                'city' => $u->city_short,
                'matching_days' => [$matchingDays ? count($matchingDays) : -1, $totalDays]
            ];
        }

        return response()->json([
            "data" => $data
        ]);
    }

    /**
     * POST: Get a list of matching days between logged in user and comparison user for the Modal
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id The ID of the comparison user
     * @return \Illuminate\Http\Response
     */
    public function gMatchingDays(Request $request, $id)
    {
        // Retrieve time frame for matching days
        [$start, $end] = $this->scheduleService->retrieveStartAndEnd();

        // If comparison user doesn't exist, return error message
        $compareUser = User::find($id);
        if(!$compareUser) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // Get matching days and total days
        $matchingDays = $this->scheduleService->getMatchingDays(Auth::user(), $compareUser, $start, $end);
        $totalDays = $this->scheduleService->getTotalDays(Auth::user(), $start, $end);
        if(!$matchingDays || $totalDays == -1) {
            return response()->json([
                'error' => 'An error occured while fetching matching days'
            ]);
        }

        // Return result as JSON
        return response()->json([
            'result' => $matchingDays,
            'totalCount' => $totalDays
        ]);
    }

    /**
     * GET: Clear matching days cache
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gClearMatchingDays(Request $request)
    {
        // Delete all entries in day_schedules and schedules
        DB::table('day_schedules')->delete();
        DB::table('schedules')->delete();

        // Return success message
        return response()->json([
            'message' => 'Successfully cleared matching days cache'
        ]);
    }

    /**
     * GET: Get all events from the specified calendar in JSON format (with cashing)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $cal The calendar to fetch
     * @return \Illuminate\Http\Response
     */
    public function gCalendar(Request $request, $cal)
    {
        $data = [];

        // Check if the calendar is valid
        if(!in_array($cal, json_decode(Storage::get('classes.json'))->courseList)) {
            return response()->json(['error' => 'Invalid course name.'], 500);
        }

        // Fetch calendar events, if required
        if(!$this->scheduleService->addOrUpdate($cal, false, true)) {
            return response()->json(['error' => 'An error occured while fetching calendar events.'], 500);
        }
        
        $schedule = DB::table('schedules')->where('class', $cal)->first();
        $daySchedules = DB::table('day_schedules')
                    ->where('schedule_id', $schedule->id)
                    ->where('day', '>=', date('Y-m-d'))
                    ->orderBy('day', 'asc')
                    ->get();

        foreach ($daySchedules as $daySchedule) {
            foreach (json_decode($daySchedule->json) as $event) {
                $data[] = [
                    'start' => $event->start,
                    'end' => $event->end,
                    'summary' => $event->summary,
                    'description' => $event->description,
                ];
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
