<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PetController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard',[PetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pet-profiles', function () {
        return Inertia::render('PetProfiles/Index');
    })->name('pet-profiles');
});

Route::get('/petProfile', [PetController::class, 'show'])->name('pet.profile');
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
Route::post('/pets/store', [PetController::class, 'store'])->middleware(['auth'])->name('pets.store');
Route::put('/petProfile', [PetController::class, 'update']);
Route::delete('/petProfile', [PetController::class, 'destroy']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
