<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use App\Models\DailyBehavior;
use App\Models\Pet;
use Illuminate\Http\Request;

class BehaviorController extends Controller
{
    public function store(Request $request, $pet)
    {
        $validated = $request->validate([
            'behaviors' => 'required|array',
            'behavior_notes' => 'nullable|string',
            'general_notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet;
        Behavior::create($validated);

        return redirect()->back()->with('success', 'Behavior information created successfully');
    }

    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'behaviors' => 'required|array',
            'behaviors.*' => 'required|string|max:255',
            'behavior_notes' => 'nullable|string',
            'general_notes' => 'nullable|string'
        ]);

        $pet->behaviors()->delete();
        $pet->behaviors()->create($validated);

        return redirect()->back()->with('success', 'Behavior information updated successfully');
    }

    public function logDailyBehavior(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'occurred_at' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $behavior = $pet->behaviors()->firstOrFail();

        DailyBehavior::create([
            'behavior_id' => $behavior->id,
            'occurred_at' => $validated['occurred_at'],
            'notes' => $validated['notes'] ?? null
        ]);

        return redirect()->back()->with('success', 'Daily behavior logged successfully');
    }
}
