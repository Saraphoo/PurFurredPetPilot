<?php
namespace App\Http\Controllers;

use App\Models\Pet;
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
        $pet = Pet::query()->create([
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
        ]);

        $pet->users()->attach(Auth::id());



        // Redirect to the dashboard
        return to_route('dashboard')->with('success', 'Pet created.');
    }

    public function show($id)
    {
        $pet = Pet::findOrFail($id);

        // Return the Inertia response with the pet's data
        return Inertia::render('Pets/Show', [
            'pet' => $pet,
        ]);
    }
}
