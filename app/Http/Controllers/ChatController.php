<?php

namespace App\Http\Controllers;

use App\AI\Chat;
use App\Models\Pet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function getPets(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get all pets for this user, either owned or shared
            $pets = Pet::where('user_id', $user->id)
                ->orWhereHas('users', function($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->select('id', 'name')
                ->get();
                
            if ($request->wantsJson()) {
                return response()->json(['pets' => $pets]);
            }
            
            return Inertia::render('Dashboard', [
                'pets' => $pets
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPets', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch pets'], 500);
        }
    }

    public function chat(Request $request)
    {
        try {
            Log::info('Chat request received', [
                'message' => $request->message,
                'pet_id' => $request->pet_id
            ]);

            $request->validate([
                'message' => 'required|string',
                'pet_id' => 'nullable|exists:pets,id'
            ]);

            Log::info('Request validated successfully');

            $chat = new Chat();
            
            // Set pet context if a pet is selected
            if ($request->pet_id) {
                Log::info('Fetching pet context', ['pet_id' => $request->pet_id]);
                $pet = Pet::with('petInfo')->find($request->pet_id);
                if (!$pet) {
                    return response()->json(['error' => 'Pet not found'], 404);
                }
                $chat->setPetContext($pet);
                Log::info('Pet context set', ['pet' => $pet->toArray()]);
            }

            // Set the system message with pet context
            $chat->systemMessage('You are a close, well-educated friend who happens to be an expert in pet care. Respond in a warm, conversational tone while sharing your knowledge. Use casual language and occasional friendly expressions, but maintain accuracy and professionalism in your advice. Share personal insights and experiences when relevant, and always prioritize the pet\'s well-being in your responses.');
            Log::info('System message set');

            try {
                Log::info('Attempting to send message to OpenAI');
                $response = $chat->send($request->message);
                Log::info('OpenAI response received', ['response' => $response]);
                return response()->json(['message' => $response]);
            } catch (\Exception $e) {
                Log::error('Chat error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
                ]);
                
                // Check if it's an API key issue
                if (str_contains($e->getMessage(), 'API key')) {
                    return response()->json([
                        'error' => 'OpenAI API configuration error. Please check your API key.'
                    ], 500);
                }
                
                return response()->json([
                    'error' => 'Error processing your request: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chat controller error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);
            return response()->json([
                'error' => 'Error processing your request: ' . $e->getMessage()
            ], 500);
        }
    }
} 