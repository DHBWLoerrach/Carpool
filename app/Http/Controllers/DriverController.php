<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * GET: Show the find drivers view
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gDrivers(Request $request)
    {
        // Get the URL parameter 'view' (default: table)
        $view = $request->input('view', 'table');

        // If view is map, return drivers view with map
        if($view == 'map') {
            // Retrieve time frame for matching days
            [$start, $end] = $this->scheduleService->retrieveStartAndEnd();

            // Get total days for logged in user in the given time frame
            $totalDays = $this->scheduleService->getTotalDays(Auth::user(), $start, $end);
            
            // Get matching days for every driver
            $mds = [];
            $users = User::where('isDriver', true)->where('id', '!=', Auth::id())->get();
            foreach($users as $u) {
                $matchingDays = $this->scheduleService->getMatchingDays(Auth::user(), $u, $start, $end);
                $matchingDaysStr = $matchingDays ? count($matchingDays) : -1;
                $mds[$u->id] = "{$matchingDaysStr}/{$totalDays}";
            }

            return view('app/drivers', [
                'view' => $view,
                'users' => $users,
                'mds' => $mds
            ]);
        } else {
            // Search query (default: '')
            $q = $request->input('q', '');

            return view('app/drivers', [
                'view' => $view,
                'users' => [],   // Not needed for table view (data is fetched via AJAX)
                'q' => $q
            ]);
        }
    }

    /**
     * GET: Show the driver profile view for the driver with the given id
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id The ID of the driver
     * @return \Illuminate\Http\Response
     */
    public function gDriver(Request $request, $id)
    {
        $view = request()->get('view', 'route');    // Get the URL parameter 'view' (default: route)
        $cuser = Auth::user();  // Current user
        $user = User::findOrFail($id); // User with the given id

        return view('app/driver-profile', compact('id', 'view', 'cuser', 'user'));
    }
}
