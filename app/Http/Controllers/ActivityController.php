<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Pet;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Pet $pet)
    {
        $this->authorize('caretake', $pet);

        return response()->json($pet->activities()->get());
    }

    public function store(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'activities' => 'required|array',
            'activities.*.name' => 'required|string',
            'activities.*.duration_value' => 'required|integer',
            'activities.*.duration_unit' => 'required|string',
            'activities.*.frequency_value' => 'required|integer',
            'activities.*.frequency_unit' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        foreach ($validated['activities'] as $activity) {
            $pet->activities()->create([
                'activity' => $activity['name'],
                'duration_value' => $activity['duration_value'],
                'duration_unit' => $activity['duration_unit'],
                'frequency_value' => $activity['frequency_value'],
                'frequency_unit' => $activity['frequency_unit'],
                'notes' => $validated['notes'] ?? null
            ]);
        }

        return response()->json($pet->activities()->get());
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('caretake', $activity->pet);

        $activity->delete(); // This will soft delete the activity
        return response()->json(['message' => 'Activity deleted successfully']);
    }
}
