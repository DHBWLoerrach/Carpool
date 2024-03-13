<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\ScheduleService;

class LoginController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * GET: Show the login form
     *
     * @return \Illuminate\Http\Response
     */
    public function gLogin()
    {
        // If logged in
        if (Auth::check()) {
            // Fully registered users are redirected to dashboard, others to the register page
            if(Auth::user()->role > 0) {
                return redirect()->intended('dashboard');
            } else {
                return redirect()->route('register');
            }
        }

        // Else, return login view
        return view('login/login');
    }

    /**
     * POST: Log the user in
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pLogin(Request $request)
    {
        // Get attributes from the request
        $userAttributes = [
            'email' => $request->post('email'),
            'name' => $request->post('name'),
            'firstname' => $request->post('firstname')
        ];

        // Validate fields and ff a field is not valid, redirect to login page with errors for that field
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'firstname' => 'required'
        ]);

        // Find or create the user and log them in
        $user = User::firstOrCreate(['email' => $userAttributes['email']], $userAttributes);
        Auth::login($user);

        // If the user's role is 0 (default value after creation), redirect to register route
        if ($user->role == 0) {
            return redirect()->route('register');
        }

        // Cache schedule
        $this->scheduleService->addOrUpdate($user->class, true);

        // Else, redirect to dashboard
        return redirect()->intended('dashboard');
    }

    /**
     * POST: Log the user out
     *
     * @return \Illuminate\Http\Response
     */
    public function pLogout()
    {
        // Logout user
        Auth::logout();
        return redirect()->route('login');
    }
}
