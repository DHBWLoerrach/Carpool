<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * GET: Show the register view
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function gRegister(Request $request)
    {
        if (!Auth::check()) {
            // If the user is not logged in, redirect to the login page
            return redirect()->intended('login');
        } else if(Auth::user()->role > 0) {
            // If the user is already fully registered, redirect to the dashboard
            return redirect()->intended('dashboard');
        }

        // If the user is logged in and not fully registered, show the register view
        return view('login/register', [
            'user' => Auth::user(),
            'classes' => json_decode(Storage::get('classes.json'))->courseList
        ]);
    }

    /**
     * POST: Register the user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pRegister(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255|min:2',
            'name' => 'required|string|max:255|min:3',
            'class' => [
                'required',
                'string',
                'max:255',
                'min:3',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, json_decode(Storage::get('classes.json'))->courseList)) {
                        $fail('Der ausgewählte Kurs ist ungültig.');
                    }
                },
            ],
            'freeSeats' => 'nullable|required_if:isDriver,1|integer|min:1',
            'terms' => 'required|accepted',
            'city' => [
                'required',
                'string',
                'max:500',
                'regex:/^[^|]+\|[^|]+\|[\d.-]+\|[\d.-]+$/'
            ],
            'notes' => 'nullable|string|max:100'
        ], [
            // Generic error message for all fields
            '*' => 'Die Eingabe ist ungültig.',
        ]);

        
        
        // If validation fails, redirect back to register view with errors
        if ($validator->fails()) {
            return redirect()->route('register')->withErrors($validator)->withInput();
        }

        // Split the city into its parts
        $cityParts = explode('|', $request->input('city'));
        if (count($cityParts) !== 4) {
            return redirect()->route('register')->withErrors($validator)->withInput();
        }
        
        // Get the user's attributes from the request
        $userAttributes = [
            'email' => $request->post('email'),
            'name' => $request->post('name'),
            'firstname' => $request->post('firstname'),
            'isDriver' => $request->post('isDriver'),
            'class' => $request->post('class'),
            'city' => $cityParts[0],
            'city_short' => $cityParts[1],
            'cityLat' => $cityParts[2],
            'cityLon' => $cityParts[3],
            'freeSeats' => $request->post('isDriver') ? $request->post('freeSeats') : 0,
            'notes' => $request->post('notes'),
            'role' => 1 // 0 = Not fully registered, 1 = Fully registered, 2 = Admin
        ];

        // Update the user's attributes
        $user = Auth::user();
        $user->update($userAttributes);

        // Cache schedule
        $this->scheduleService->addOrUpdate($user->class, true);
        
        return redirect()->route('dashboard');
    }
}
