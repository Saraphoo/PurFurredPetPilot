<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PetCaretakerController extends Controller
{
    public function index(Pet $pet)
    {
        $this->authorize('view', $pet);

        return response()->json(
            $pet->users()->select('users.id', 'users.name', 'users.email')->get()
        );
    }

    public function store(Request $request, Pet $pet)
    {
        $this->authorize('manageCaretakers', $pet);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:' . Pet::ROLE_OWNER . ',' . Pet::ROLE_CARETAKER,
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($pet->hasCaretaker($user)) {
            throw ValidationException::withMessages([
                'email' => 'This person is already a caretaker for this pet.',
            ]);
        }

        $pet->users()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('success', 'Caretaker added.');
    }

    public function update(Request $request, Pet $pet, User $user)
    {
        $this->authorize('manageCaretakers', $pet);

        $validated = $request->validate([
            'role' => 'required|in:' . Pet::ROLE_OWNER . ',' . Pet::ROLE_CARETAKER,
        ]);

        $this->guardAgainstRemovingLastOwner($pet, $user, $validated['role']);

        $pet->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return back()->with('success', 'Caretaker role updated.');
    }

    public function destroy(Pet $pet, User $user)
    {
        $this->authorize('manageCaretakers', $pet);

        $this->guardAgainstRemovingLastOwner($pet, $user, null);

        $pet->users()->detach($user->id);

        return back()->with('success', 'Caretaker removed.');
    }

    /**
     * Prevent a pet from ever being left with zero owners.
     */
    private function guardAgainstRemovingLastOwner(Pet $pet, User $user, ?string $newRole): void
    {
        $isCurrentlyOwner = $pet->owners()->where('users.id', $user->id)->exists();
        $wouldStillBeOwner = $newRole === Pet::ROLE_OWNER;

        if ($isCurrentlyOwner && !$wouldStillBeOwner && $pet->owners()->count() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'A pet must always have at least one owner.',
            ]);
        }
    }
}
