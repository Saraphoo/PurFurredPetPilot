<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use App\Models\DailyBehavior;
use App\Models\Pet;
use Illuminate\Http\Request;

class BehaviorController extends Controller
{
    public function index(Pet $pet)
    {
        $this->authorize('caretake', $pet);

        return response()->json($pet->behaviors()->first());
    }

    public function store(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'behaviors' => 'required|array',
            'behaviors.*' => 'required|string|max:255',
            'behavior_notes' => 'nullable|string',
            'general_notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet->id;
        $behavior = Behavior::create($validated);

        return response()->json($behavior, 201);
    }

    public function update(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'behaviors' => 'required|array',
            'behaviors.*' => 'required|string|max:255',
            'behavior_notes' => 'nullable|string',
            'general_notes' => 'nullable|string'
        ]);

        $pet->behaviors()->delete();
        $behavior = $pet->behaviors()->create($validated);

        return response()->json($behavior);
    }

    public function logDailyBehavior(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

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

        return response()->json(['message' => 'Daily behavior logged successfully']);
    }
}
