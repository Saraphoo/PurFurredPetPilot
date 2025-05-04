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
            'special_needs.*.name' => 'required|string|max:255',
            'special_needs.*.affects' => 'required|string|max:255',
            'special_needs.*.notes' => 'nullable|string',
            'medications' => 'required|array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.prescribed_on' => 'required|date',
            'medications.*.notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Delete existing records
        SpecialNeed::where('pet_id', $pet)->delete();
        Medication::where('pet_id', $pet)->delete();

        // Create special needs
        foreach ($validated['special_needs'] as $need) {
            $need['pet_id'] = $pet;
            SpecialNeed::create($need);
        }

        // Create medications
        foreach ($validated['medications'] as $medication) {
            $medication['pet_id'] = $pet;
            Medication::create([
                'pet_id' => $pet,
                'medication_name' => $medication['name'],
                'prescribed_on' => $medication['prescribed_on'],
                'notes' => $medication['notes']
            ]);
        }

        return redirect()->back()->with('success', 'Medical information saved successfully');
    }

    public function update(Request $request, $pet)
    {
        return $this->store($request, $pet);
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