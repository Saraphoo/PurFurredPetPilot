<?php

namespace App\Http\Controllers;

use App\AI\Chat;
use App\Models\Pet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function getPets(Request $request)
    {
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
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'pet_id' => 'nullable|exists:pets,id'
        ]);

        $chat = new Chat();
        
        // Set pet context if a pet is selected
        if ($request->pet_id) {
            $pet = Pet::with('petInfo')->find($request->pet_id);
            $chat->setPetContext($pet);
        }

        // Set the system message with pet context
        $chat->systemMessage("You are a helpful pet care assistant. Provide accurate and helpful information about pet care, health, and well-being.");

        $response = $chat->send($request->message);

        if (!$response) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to get response from AI'], 500);
            }
            return back()->with('error', 'Failed to get response from AI');
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $response]);
        }
        return back()->with('message', $response);
    }
} 