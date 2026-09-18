<?php
namespace App\Http\Controllers;

use App\Models\Pet;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
class PetController extends Controller
{
    public function index()
    {
        // Get the currently authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Retrieve pets this user is a caretaker of, in any role
        $pets = $user->pets;

        // Pass the pets to the Inertia view
        return Inertia::render('Dashboard', [
            'pets' => $pets
        ]);
    }

    public function create()
    {
        return Inertia::render('pets/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'DOB' => 'required|date',
            'sex' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'species' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:255',
            'neutered' => 'nullable|boolean',
            'color' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:255',
            'length' => 'nullable|string|max:255',
        ]);

        $pet = Pet::create($validated);
        $pet->users()->attach(Auth::id(), ['role' => Pet::ROLE_OWNER]);

        // Redirect to the dashboard
        return to_route('dashboard')->with('success', 'Pet created.');
    }

    public function show(Pet $pet)
    {
        $this->authorize('view', $pet);

        $pet->load('petInfo');

        // Each care-tracking form (meals, medical, behavior, housing, activities)
        // loads its own data client-side from its own index endpoint.
        return Inertia::render('pets/Show', [
            'pet' => $pet,
            'petInfo' => $pet->petInfo,
        ]);
    }

    public function storePetInfo(Request $request, Pet $pet)
    {
        $this->authorize('caretake', $pet);

        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        // Create or update the PetInfo
        $pet->petInfo()->updateOrCreate(
            ['key' => $request->input('key')],
            ['value' => $request->input('value')]
        );

        return back()->with('success', 'Pet information saved.');
    }

    public function update(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'DOB' => 'required|date',
            'sex' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'species' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:255',
            'neutered' => 'nullable|boolean',
            'color' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:255',
            'length' => 'nullable|string|max:255',
        ]);

        $pet->update($validated);

        return back()->with('success', 'Pet updated.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        $this->authorize('delete', $pet);

        $pet->delete();

        return to_route('dashboard')->with('success', 'Pet deleted.');
    }

}
