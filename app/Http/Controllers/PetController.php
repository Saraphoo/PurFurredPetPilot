<?php
namespace App\Http\Controllers;

use App\Models\Pet;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Retrieve pets associated with the user
        $pets = $user->pets;

        // Pass the pets to the Inertia view
        return Inertia::render('Dashboard', [
            'pets' => $pets
        ]);
    }

    public function show()
    {
        return Inertia::render('PetProfile');
    }

    public function store()
    {
        // Validate the request
        request()->validate([
            'name' => 'required',
            'type' => 'required',
            'breed' => 'required',
            'DOB' => 'required',
        ]);

        // Create a new pet
        Pet::create([
            'name' => request('name'),
            'type' => request('type'),
            'breed' => request('breed'),
            'DOB' => request('age'),
            'user_id' => Auth::id(),
        ]);

        // Redirect to the dashboard
        return redirect()->route('dashboard');
    }
}
