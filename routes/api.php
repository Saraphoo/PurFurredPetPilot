Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/pets', [App\Http\Controllers\ChatController::class, 'getPets']);
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'chat']);
}); 