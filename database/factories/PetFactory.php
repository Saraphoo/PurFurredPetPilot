<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    // Define the corresponding model
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName(),
            'DOB' => $this->faker->date(),
            'type' => $this->faker->randomElement(['Dog', 'Cat', 'Bird', 'Fish']),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'species' => $this->faker->word(),
        ];
    }

    /**
     * Attach the given user as this pet's owner once it's created.
     */
    public function ownedBy(User $user): static
    {
        return $this->afterCreating(function (Pet $pet) use ($user) {
            $pet->users()->attach($user->id, ['role' => Pet::ROLE_OWNER]);
        });
    }

    /**
     * Attach the given user as a (non-owner) caretaker once it's created.
     */
    public function caretakenBy(User $user): static
    {
        return $this->afterCreating(function (Pet $pet) use ($user) {
            $pet->users()->attach($user->id, ['role' => Pet::ROLE_CARETAKER]);
        });
    }
}
