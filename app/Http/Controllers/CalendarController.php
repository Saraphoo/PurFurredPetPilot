<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Pet;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', auth()->id())->get();
        $pets = Pet::where('user_id', auth()->id())
            ->select('id', 'name', 'type', 'DOB')
            ->get();

        return Inertia::render('Calendar', [
            'events' => $events,
            'pets' => $pets
        ]);
    }

    public function events()
    {
        try {
            $events = Event::where('user_id', auth()->id())->get();
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function connect()
    {
        // Google Calendar connection logic will go here
        return response()->json(['url' => 'https://accounts.google.com/o/oauth2/auth/...']);
    }
} 