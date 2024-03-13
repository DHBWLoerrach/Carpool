<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * GET: Show the dashboard view
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gDashboard(Request $request)
    {
        return view('app/dashboard', [
            'user_count' => User::count(),
            'driver_count' => User::where('isDriver', 1)->count(),
            'class_count' => count(User::distinct('class')->pluck('class')),
            'users' => User::where('isDriver', 1)->where('id', '!=', Auth::id())->get()->take(-3)
        ]);
    }
}
