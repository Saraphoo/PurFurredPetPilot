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
        return Inertia::render('Pets/Index', [
            'pets' => $pets
        ]);
    }
}
