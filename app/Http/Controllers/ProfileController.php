<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use \App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * GET: Show the profile view
     * 
     * @return \Illuminate\Http\Response
     */
    public function gProfile()
    {
        $user = Auth::user();
        return view('app/my-profile', compact('user'));
    }

    /**
     * POST: Update the user's profile
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pProfile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
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
        
        // If validation fails, redirect back to profile view with errors
        if ($validator->fails()) {
            return redirect()->route('profile')->withErrors($validator)->withInput();
        }

        // Split the city into its parts
        $cityParts = explode('|', $request->input('city'));

        // Get the user's attributes from the request
        $userAttributes = [
            'isDriver' => $request->post('isDriver'),
            'class' => $request->post('class'),
            'city' => $cityParts[0],
            'city_short' => $cityParts[1],
            'cityLat' => $cityParts[2],
            'cityLon' => $cityParts[3],
            'freeSeats' => $request->post('freeSeats'),
            'notes' => $request->post('notes')
        ];

        // Update the user's attributes
        $user = Auth::user();
        $oldClass = $user->class;
        $user->update($userAttributes);

        // Cache schedule
        $this->scheduleService->addOrUpdate($user->class, $oldClass != $user->class, true);
        return redirect()->route('profile');
    }
}
