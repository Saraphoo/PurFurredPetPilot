<?php
namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Medical;
use App\Models\Meal;
use App\Models\Behavior;
use App\Models\Housing;
use App\Models\Activity;
use App\Models\SpecialNeed;
use App\Models\Medication;
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
        
        // Initialize initialData with all form data
        $initialData = [
            'medical' => [
                'special_needs' => SpecialNeed::where('pet_id', $pet->id)->get()->map(function($need) {
                    return [
                        'name' => $need->name,
                        'affects' => $need->affects,
                        'notes' => $need->notes
                    ];
                }),
                'medications' => Medication::where('pet_id', $pet->id)->get()->map(function($medication) {
                    return [
                        'name' => $medication->medication_name,
                        'prescribed_on' => $medication->prescribed_on,
                        'notes' => $medication->notes
                    ];
                }),
                'notes' => SpecialNeed::where('pet_id', $pet->id)->value('notes')
            ],
            'meals' => [
                'meals' => Meal::where('pet_id', $pet->id)->get()->map(function($meal) {
                    return [
                        'feed_time' => $meal->feed_time,
                        'food_name' => $meal->food_name,
                        'brand' => $meal->brand,
                        'meal_type' => $meal->meal_type,
                        'serving_value' => $meal->serving_value,
                        'serving_unit' => $meal->serving_unit
                    ];
                }),
                'notes' => Meal::where('pet_id', $pet->id)->value('notes')
            ],
            'behavior' => [
                'behaviors' => Behavior::where('pet_id', $pet->id)->get()->pluck('name'),
                'behavior_notes' => Behavior::where('pet_id', $pet->id)->value('behavior_notes'),
                'general_notes' => Behavior::where('pet_id', $pet->id)->value('general_notes')
            ],
            'housing' => [
                'total_space_value' => Housing::where('pet_id', $pet->id)->value('total_space_value'),
                'total_space_unit' => Housing::where('pet_id', $pet->id)->value('total_space_unit'),
                'housing_type' => Housing::where('pet_id', $pet->id)->value('housing_type'),
                'flooring_type' => Housing::where('pet_id', $pet->id)->value('flooring_type'),
                'bedding_type' => Housing::where('pet_id', $pet->id)->value('bedding_type'),
                'accessories' => Housing::where('pet_id', $pet->id)->get()->map(function($housing) {
                    return [
                        'type' => $housing->accessory_type,
                        'name' => $housing->accessory_name,
                        'size' => $housing->accessory_size,
                        'brand' => $housing->accessory_brand,
                        'material' => $housing->accessory_material,
                        'notes' => $housing->accessory_notes
                    ];
                }),
                'notes' => Housing::where('pet_id', $pet->id)->value('notes')
            ],
            'activities' => Activity::where('pet_id', $pet->id)->get()->map(function($activity) {
                return [
                    'name' => $activity->activity,
                    'duration_value' => $activity->duration_value,
                    'duration_unit' => $activity->duration_unit,
                    'frequency_value' => $activity->frequency_value,
                    'frequency_unit' => $activity->frequency_unit
                ];
            })
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
