<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Pet;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function store(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'medication_type' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'prescribed_on' => 'nullable|string|max:255',
            'frequency_value' => 'nullable|integer',
            'frequency_unit' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet->id;
        $medication = Medication::create($validated);

        return redirect()->back()->with('success', 'Medication information created successfully');
    }

    public function update(Request $request, Pet $pet, Medication $medication)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'medication_type' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'prescribed_on' => 'nullable|string|max:255',
            'frequency_value' => 'nullable|integer',
            'frequency_unit' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $medication->update($validated);

        return redirect()->back()->with('success', 'Medication updated successfully');
    }

    public function destroy(Pet $pet, Medication $medication)
    {
        $this->authorize('caretake', $pet);

        $medication->delete();
        return redirect()->back()->with('success', 'Medication deleted successfully');
    }
}
