<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\DailyMeal;
use App\Models\Pet;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function store(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'feed_time' => 'required|date_format:H:i',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'meal_type' => 'required|string|max:255',
            'portion_size' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $validated['pet_id'] = $pet->id;
        $meal = Meal::create($validated);

        return redirect()->back()->with('success', 'Meal schedule created successfully');
    }

    public function update(Request $request, Pet $pet, Meal $meal)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'feed_time' => 'required|date_format:H:i',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'meal_type' => 'required|string|max:255',
            'portion_size' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $meal->update($validated);

        return redirect()->back()->with('success', 'Meal schedule updated successfully');
    }

    public function destroy(Pet $pet, Meal $meal)
    {
        $this->authorize('caretake', $pet);

        $meal->delete();
        return redirect()->back()->with('success', 'Meal schedule deleted successfully');
    }

    public function logDailyMeal(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $validated = $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'fed_at' => 'required|date',
            'portions_fed' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        DailyMeal::create($validated);

        return redirect()->back()->with('success', 'Daily meal logged successfully');
    }
}
