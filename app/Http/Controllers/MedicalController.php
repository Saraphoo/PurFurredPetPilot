<?php

namespace App\Http\Controllers;

use App\Models\SpecialNeed;
use App\Models\Medication;
use App\Models\DailyMedication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalController extends Controller
{
    public function store(Request $request, $pet)
    {
        $validated = $request->validate([
            'special_needs' => 'required|array',
            'medications' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet;
        $medical = Medical::create($validated);

        return redirect()->back()->with('success', 'Medical information created successfully');
    }

    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'special_needs' => 'array',
            'special_needs.*.name' => 'required|string|max:255',
            'special_needs.*.affects' => 'required|string|max:255',
            'special_needs.*.notes' => 'nullable|string',
            'medications' => 'array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.prescribed_on' => 'required|date',
            'medications.*.notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Update special needs
        if (isset($validated['special_needs'])) {
            $pet->specialNeeds()->delete();
            foreach ($validated['special_needs'] as $need) {
                $pet->specialNeeds()->create($need);
            }
        }

        // Update medications
        if (isset($validated['medications'])) {
            $pet->medications()->delete();
            foreach ($validated['medications'] as $medication) {
                $pet->medications()->create($medication);
            }
        }

        return redirect()->back()->with('success', 'Medical information updated successfully');
    }

    public function logDailyMedication(Request $request)
    {
        $validated = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'date' => 'required|date',
            'was_given' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        DailyMedication::create($validated);

        return redirect()->back()->with('success', 'Daily medication logged successfully');
    }
} 