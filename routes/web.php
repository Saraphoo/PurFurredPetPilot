<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PetController;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use App\Http\Controllers\MealController;
use App\Http\Controllers\HousingController;
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\BehaviorController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [PetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pets/show', function () {
        return Inertia::render('pets/Show');
    })->name('pets.show');

    Route::get('/calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar.index');

    // Chat routes
    Route::get('/user/pets', [ChatController::class, 'getPets']);
    Route::post('/chat', [ChatController::class, 'chat'])->name('chat.message');

    // Event routes
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Meal routes
    Route::post('/pets/{pet}/meals', [MealController::class, 'store'])->name('meals.store');
    Route::put('/pets/{pet}/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::delete('/pets/{pet}/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
    Route::post('/pets/{pet}/meals/log', [MealController::class, 'logDailyMeal'])->name('meals.log');

    // Housing routes
    Route::post('/pets/{pet}/housing', [HousingController::class, 'store'])->name('housing.store');
    Route::put('/pets/{pet}/housing/{housing}', [HousingController::class, 'update'])->name('housing.update');
    Route::delete('/pets/{pet}/housing/{housing}', [HousingController::class, 'destroy'])->name('housing.destroy');

    // Medical routes
    Route::post('/pets/{pet}/medical', [MedicalController::class, 'store'])->name('medical.store');
    Route::put('/pets/{pet}/medical', [MedicalController::class, 'update'])->name('medical.update');
    Route::post('/pets/{pet}/medical/log', [MedicalController::class, 'logDailyMedication'])->name('medical.log');

    // Behavior routes
    Route::post('/pets/{pet}/behaviors', [BehaviorController::class, 'store'])->name('behaviors.store');
    Route::put('/pets/{pet}/behaviors', [BehaviorController::class, 'update'])->name('behaviors.update');
    Route::post('/pets/{pet}/behaviors/log', [BehaviorController::class, 'logDailyBehavior'])->name('behaviors.log');

    // Activity routes
    Route::get('/pets/{pet}/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/pets/{pet}/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::put('/pets/{pet}/activities', [ActivityController::class, 'update'])->name('activities.update');
    Route::post('/pets/{pet}/activities/log', [ActivityController::class, 'logDailyActivity'])->name('activities.log');

    // Medication routes
    Route::post('/pets/{pet}/medications', [MedicationController::class, 'store'])->name('medications.store');
    Route::put('/pets/{pet}/medications/{medication}', [MedicationController::class, 'update'])->name('medications.update');
    Route::delete('/pets/{pet}/medications/{medication}', [MedicationController::class, 'destroy'])->name('medications.destroy');
});

Route::get('/users/{user}', function (User $user) {
    return $user->email;
});

Route::get('/pets/show/{pet}', [PetController::class, 'show'])->name('pet.show');
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
Route::post('/pets/store', [PetController::class, 'store'])->middleware(['auth'])->name('pets.store');
Route::put('/pets/show', [PetController::class, 'update']);
Route::delete('/pets/show', [PetController::class, 'destroy']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
