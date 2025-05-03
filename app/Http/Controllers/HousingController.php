<?php

namespace App\Http\Controllers;

use App\Models\Housing;
use App\Models\HousingAccessory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HousingController extends Controller
{
    public function store(Request $request, $pet)
    {
        $validated = $request->validate([
            'total_space_value' => 'required|numeric',
            'total_space_unit' => 'required|string|max:50',
            'housing_type' => 'required|string|max:255',
            'flooring_type' => 'required|string|max:255',
            'bedding_type' => 'required|string|max:255',
            'accessories' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet;
        $housing = Housing::create($validated);

        // Handle accessories if provided
        if ($request->has('accessories')) {
            foreach ($request->accessories as $accessory) {
                $housing->accessories()->create([
                    'accessory_type' => $accessory['type'],
                    'name' => $accessory['name'],
                    'size' => $accessory['size'],
                    'brand' => $accessory['brand'],
                    'material' => $accessory['material'],
                    'notes' => $accessory['notes'] ?? null
                ]);
            }
        }

        return redirect()->back()->with('success', 'Housing information created successfully');
    }

    public function update(Request $request, Housing $housing)
    {
        $validated = $request->validate([
            'total_space_value' => 'required|numeric',
            'total_space_unit' => 'required|string|max:50',
            'housing_type' => 'required|string|max:255',
            'flooring_type' => 'required|string|max:255',
            'bedding_type' => 'required|string|max:255',
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
                    'size' => $accessory['size'],
                    'brand' => $accessory['brand'],
                    'material' => $accessory['material'],
                    'notes' => $accessory['notes'] ?? null
                ]);
            }
        }

        return redirect()->back()->with('success', 'Housing information updated successfully');
    }

    public function destroy(Housing $housing)
    {
        $housing->accessories()->delete();
        $housing->delete();
        return redirect()->back()->with('success', 'Housing information deleted successfully');
    }
} 