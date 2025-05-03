<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicationController extends Controller
{
    public function store(Request $request, $pet)
    {
        $validated = $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'prescribing_vet' => 'nullable|string|max:255',
            'pharmacy' => 'nullable|string|max:255',
            'refill_date' => 'nullable|date',
            'expiration_date' => 'nullable|date'
        ]);

        $validated['pet_id'] = $pet;
        $medication = Medication::create($validated);

        return redirect()->back()->with('success', 'Medication information created successfully');
    }

    public function update(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'prescribing_vet' => 'nullable|string|max:255',
            'pharmacy' => 'nullable|string|max:255',
            'refill_date' => 'nullable|date',
            'expiration_date' => 'nullable|date'
        ]);

        $medication->update($validated);

        return redirect()->back()->with('success', 'Medication updated successfully');
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();
        return redirect()->back()->with('success', 'Medication deleted successfully');
    }
} 