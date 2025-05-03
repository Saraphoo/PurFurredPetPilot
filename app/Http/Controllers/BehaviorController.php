<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use App\Models\DailyBehavior;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $behavior = Behavior::create($validated);

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

        // Update behaviors
        $pet->behaviors()->delete();
        foreach ($validated['behaviors'] as $behavior) {
            $pet->behaviors()->create([
                'name' => $behavior,
                'notes' => $validated['behavior_notes'] ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Behavior information updated successfully');
    }

    public function logDailyBehavior(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'date' => 'required|date',
            'behaviors' => 'required|array',
            'behaviors.*.name' => 'required|string|max:255',
            'behaviors.*.observed' => 'required|boolean',
            'behaviors.*.notes' => 'nullable|string'
        ]);

        foreach ($validated['behaviors'] as $behavior) {
            DailyBehavior::create([
                'pet_id' => $validated['pet_id'],
                'date' => $validated['date'],
                'behavior_name' => $behavior['name'],
                'observed' => $behavior['observed'],
                'notes' => $behavior['notes'] ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Daily behaviors logged successfully');
    }
} 