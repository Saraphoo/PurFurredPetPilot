<?php
namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Support\Facades\Log;
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

    public function store()
    {
        // Validate the request
        request()->validate([
            'name' => 'required',
            'sex' => 'nullable',
            'type' => 'required',
            'breed' => 'required',
            'age' => 'required',
        ]);
        Log::info(request('name'));
        // Create a new pet
        Pet::query()->create([
            'name' => request('name'),
            'type' => request('type'),
            'sex' => 'f',
            'breed' => request('breed'),
            'DOB' => request('age'),
        ]);

        // Redirect to the dashboard
        return redirect()->route('dashboard');
    }
}
