<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', Auth::id())
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_time,
                    'end' => $event->end_time,
                    'description' => $event->description,
                    'color' => $event->color,
                ];
            });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'pet_id' => 'required|exists:pets,id',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($event, 201);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'pet_id' => 'required|exists:pets,id',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        $event->delete();
        
        return response()->json(null, 204);
    }
} 