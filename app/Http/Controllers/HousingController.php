<?php

namespace App\Http\Controllers;

use App\Models\Housing;
use App\Models\Pet;
use Illuminate\Http\Request;

class HousingController extends Controller
{
    public function index(Pet $pet)
    {
        $this->authorize('caretake', $pet);

        return response()->json($pet->housing()->with('accessories')->get());
    }

    public function store(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'total_space_value' => 'required|numeric',
            'total_space_unit' => 'required|string|max:50',
            'housing_type' => 'required|string|max:255',
            'flooring_type' => 'required|string|max:255',
            'bedding_type' => 'required|string|max:255',
            'accessories' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet->id;
        $housing = Housing::create($validated);

        // Handle accessories if provided
        if ($request->has('accessories')) {
            foreach ($request->accessories as $accessory) {
                $housing->accessories()->create([
                    'accessory_type' => $accessory['type'],
                    'name' => $accessory['name'],
                    'accessory_size' => $accessory['size'],
                    'brand' => $accessory['brand'],
                    'material' => $accessory['material'],
                    'notes' => $accessory['notes'] ?? null
                ]);
            }
        }

        return response()->json($housing->load('accessories'), 201);
    }

    public function update(Request $request, Pet $pet, Housing $housing)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'total_space_value' => 'required|numeric',
            'total_space_unit' => 'required|string|max:50',
            'housing_type' => 'required|string|max:255',
            'flooring_type' => 'required|string|max:255',
            'bedding_type' => 'required|string|max:255',
            'accessories' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $housing->update($validated);

        // Update accessories if provided
        if ($request->has('accessories')) {
            $housing->accessories()->delete(); // Remove existing accessories
            foreach ($request->accessories as $accessory) {
                $housing->accessories()->create([
                    'accessory_type' => $accessory['type'],
                    'name' => $accessory['name'],
                    'accessory_size' => $accessory['size'],
                    'brand' => $accessory['brand'],
                    'material' => $accessory['material'],
                    'notes' => $accessory['notes'] ?? null
                ]);
            }
        }

        return response()->json($housing->load('accessories'));
    }

    public function destroy(Pet $pet, Housing $housing)
    {
        $this->authorize('caretake', $pet);

        $housing->accessories()->delete();
        $housing->delete();

        return response()->json(['message' => 'Housing information deleted successfully']);
    }
}
