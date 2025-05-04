<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PetController;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\HousingController;
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\BehaviorController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ChatController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [PetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pets/show', function () {
        return Inertia::render('pets/Show');
    })->name('pets.show');

    Route::get('/api/user/pets', [ChatController::class, 'getPets']);
    Route::post('/api/chat', [ChatController::class, 'chat'])->name('chat.message');

    // Google Calendar Routes
    Route::get('/calendar/connect', [GoogleCalendarController::class, 'connect'])->name('calendar.connect');
    Route::get('/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('calendar.callback');
    Route::get('/calendar/events', [GoogleCalendarController::class, 'listEvents'])->name('calendar.events');
    Route::post('/calendar/events', [GoogleCalendarController::class, 'createEvent'])->name('calendar.store');

    Route::get('/calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar.index');

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

//Route::get('/', function(){
//    $chat = new \App\AI\Chat();
//    $response= $chat
//        ->systemMessage('You are acting as an expert assistant')
//        ->send('message to the chatbot here');
//    return view('chatbot', ['response' => $response]);
//});
//
//
//Route::get('/', function() {
////    dd(new \App\AI\PetPilotAssistant(config('services.openai.assistant')));
//    $assistant = new \App\AI\PetPilotAssistant(config('services.openai.assistant'));
//
//    $assistant->acceptFile(storage_path('docs/file.md'));
//
//    $run = OpenAI::threads()->createAndRun([
//        'assistant_id' => $assistant->getId(),
//        'thread'=>[
//            'messages'=>[
//                ['role'=>'user', 'content'=>'message to the chatbot here']
//            ],
//            ]
//        ]);
//    do {
//        sleep(1);
//        $run = OpenAI::threads()->runs()->retrieve(
//            threadId: $run->threadId,
//            runId: $run->id
//        );
//    }while($run->status !== 'completed');
//
//    OpenAI::threads()->messages()->list($run->threadId);
//   });


//Route::post('/dashboard/test-openai', function (Request $request) {
//    $userMessage = $request->input('message');
//
//    // OpenAI request with user's message
//    $response = OpenAI::completions()->create([
//        'model' => 'gpt-4o-mini',
//        'prompt' => $userMessage,
//        'max_tokens' => 10, // Adjust based on desired response length
//    ]);
//
//    return response()->json(['message' => $response->choices[0]->text]);
//});

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
