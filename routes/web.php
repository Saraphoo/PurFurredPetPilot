<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PetController;
use OpenAI\Laravel\Facades\OpenAI;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [PetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pets/show', function () {
        return Inertia::render('pets/Show');
    })->name('pets.show');
});

Route::get('/', function(){
    $chat = new \App\AI\Chat();
    $response= $chat
        ->systemMessage('You are acting as an expert assistant')
        ->send('message to the chatbot here');
    return view('chatbot', ['response' => $response]);
});


Route::post('/dashboard/test-openai', function (Request $request) {
    $userMessage = $request->input('message');

    // OpenAI request with user's message
    $response = OpenAI::completions()->create([
        'model' => 'gpt-4o-mini',
        'prompt' => $userMessage,
        'max_tokens' => 10, // Adjust based on desired response length
    ]);

    return response()->json(['message' => $response->choices[0]->text]);
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
