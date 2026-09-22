<?php

namespace App\Http\Controllers;

use App\AI\Chat;
use App\Models\ChatSession;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function getPets(Request $request)
    {
        try {
            $user = auth()->user();

            // Get all pets this user is a caretaker of, in any role
            $pets = $user->pets()->get(['pets.id', 'pets.name']);


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
        $request->validate([
            'message' => 'required|string',
            'pet_id' => 'nullable|exists:pets,id',
            'chat_session_id' => 'nullable|string'
        ]);

        $pet = null;
        if ($request->pet_id) {
            $pet = Pet::with('petInfo')->findOrFail($request->pet_id);
            $this->authorize('caretake', $pet);
        }

        try {
            // Get or create chat session
            $chatSession = null;
            if ($request->chat_session_id) {
                $chatSession = ChatSession::where('id', $request->chat_session_id)
                    ->where('user_id', auth()->id())
                    ->first();
            }

            if (!$chatSession) {
                $chatSession = ChatSession::create([
                    'user_id' => auth()->id(),
                    'pet_id' => $request->pet_id,
                    'title' => 'New Chat ' . now()->format('M d, Y H:i')
                ]);
            }

            $chat = new Chat();

            if ($pet) {
                $chat->setPetContext($pet);
            }

            $chat->systemMessage();

            $response = $chat->send($request->message);

            $chatSession->messages()->create([
                'role' => 'user',
                'content' => $request->message
            ]);
            $chatSession->messages()->create([
                'role' => 'assistant',
                'content' => $response
            ]);

            return response()->json([
                'message' => $response,
                'chat_session_id' => $chatSession->id
            ]);
        } catch (\Exception $e) {
            Log::error('Chat controller error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (str_contains($e->getMessage(), 'API key')) {
                return response()->json(['error' => 'OpenAI API configuration error. Please check your API key.'], 500);
            }

            return response()->json(['error' => 'Error processing your request: ' . $e->getMessage()], 500);
        }
    }
} 