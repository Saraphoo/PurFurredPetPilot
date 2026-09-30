<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    /**
     * Any caretaker (owner or caretaker) can view the pet.
     */
    public function view(User $user, Pet $pet): bool
    {
        return $pet->hasCaretaker($user);
    }

    /**
     * Any caretaker (owner or caretaker) can log/manage day-to-day care for the pet.
     */
    public function caretake(User $user, Pet $pet): bool
    {
        return $pet->hasCaretaker($user);
    }

    /**
     * Only an owner can edit the pet's profile.
     */
    public function update(User $user, Pet $pet): bool
    {
        return $pet->isOwnedBy($user);
    }

    /**
     * Only an owner can delete the pet.
     */
    public function delete(User $user, Pet $pet): bool
    {
        return $pet->isOwnedBy($user);
    }

    /**
     * Only an owner can add, remove, or change the role of caretakers.
     */
    public function manageCaretakers(User $user, Pet $pet): bool
    {
        return $pet->isOwnedBy($user);
    }
}
