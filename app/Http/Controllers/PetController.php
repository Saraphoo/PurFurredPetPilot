<?php
namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Medical;
use App\Models\Meal;
use App\Models\Behavior;
use App\Models\Housing;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
class PetController extends Controller
{
    public function index()
    {
        // Get the currently authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Retrieve pets associated with the user
        $pets = $user->pets;
        Log::info($pets);

        // Pass the pets to the Inertia view
        return Inertia::render('Dashboard', [
            'pets' => $pets
        ]);
    }

    public function create()
    {
        return Inertia::render('pets/Create');
    }

    public function store(): RedirectResponse
    {
        // Validate the request
        request()->validate([
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

        // Create a new pet
        $pet = Pet::create([
            'name' => request('name'),
            'DOB' => request('DOB'),
            'type' => request('type'),
            'sex' => request('sex'),
            'species' => request('species'),
            'breed' => request('breed'),
            'neutered' => request('neutered'),
            'color' => request('color'),
            'weight' => request('weight'),
            'height' => request('height'),
            'length' => request('length'),
            'user_id' => Auth::id()
        ]);

        // Redirect to the dashboard
        return to_route('dashboard')->with('success', 'Pet created.');
    }

    public function show(Pet $pet)
    {
        $pet->load('petInfo');
        
        // Load initial data for each form
        $initialData = [
            'medical' => Medical::where('pet_id', $pet->id)->first(),
            'meals' => Meal::where('pet_id', $pet->id)->first(),
            'behavior' => Behavior::where('pet_id', $pet->id)->first(),
            'housing' => Housing::where('pet_id', $pet->id)->first(),
            'activities' => Activity::where('pet_id', $pet->id)->first(),
        ];

        // Return the Inertia response with the pet's data
        return Inertia::render('pets/Show', [
            'pet' => $pet,
            'petInfo' => $pet->petInfo,
            'initialData' => $initialData
        ]);
    }

    public function storePetInfo(Request $request, Pet $pet)
    {
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

}
