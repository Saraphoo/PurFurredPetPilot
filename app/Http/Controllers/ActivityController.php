<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    public function index(Pet $pet)
    {
        Log::info('Fetching activities for pet:', ['pet_id' => $pet->id]);
        $activities = $pet->activities()->get();
        Log::info('Found activities:', ['activities' => $activities->toArray()]);
        return response()->json($activities);
    }

    public function store(Request $request, Pet $pet)
    {
        Log::info('Storing activities for pet:', [
            'pet_id' => $pet->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'activities' => 'required|array',
            'activities.*.name' => 'required|string',
            'activities.*.duration_value' => 'required|integer',
            'activities.*.duration_unit' => 'required|string',
            'activities.*.frequency_value' => 'required|integer',
            'activities.*.frequency_unit' => 'required|string',
        ]);

        // Delete existing activities
        $pet->activities()->delete();

        // Create new activities
        foreach ($validated['activities'] as $activity) {
            $created = $pet->activities()->create([
                'activity' => $activity['name'],
                'duration_value' => $activity['duration_value'],
                'duration_unit' => $activity['duration_unit'],
                'frequency_value' => $activity['frequency_value'],
                'frequency_unit' => $activity['frequency_unit']
            ]);
            Log::info('Created activity:', ['activity' => $created->toArray()]);
        }

        return response()->json(['message' => 'Activities saved successfully']);
    }
} 