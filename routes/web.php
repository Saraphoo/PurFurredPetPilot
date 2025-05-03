<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PetController;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use App\Http\Controllers\GoogleCalendarController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [PetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pets/show', function () {
        return Inertia::render('pets/Show');
    })->name('pets.show');

    Route::post('/chat', function (Request $request) {
        try {
            $chat = new \App\AI\Chat();
            $response = $chat
                ->systemMessage('You are a helpful pet care assistant, knowledgeable about pets and their needs.')
                ->send($request->input('message'));

            if (empty($response)) {
                throw new \Exception('Received empty response from the assistant');
            }

            return response()->json([
                'message' => $response,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing your request: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    })->name('chat.message');

    // Google Calendar Routes
    Route::get('/calendar/connect', [GoogleCalendarController::class, 'connect'])->name('calendar.connect');
    Route::get('/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('calendar.callback');
    Route::get('/calendar/events', [GoogleCalendarController::class, 'listEvents'])->name('calendar.events');
    Route::post('/calendar/events', [GoogleCalendarController::class, 'createEvent'])->name('calendar.store');

    Route::get('/calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar.index');
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
