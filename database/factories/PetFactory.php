<?php

namespace Database\Factories;

use App\Models\Pet;
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
            'name' => $this->faker->firstName(), // Generate a random pet name
            'DOB' => $this->faker->date(),       // Generate a random date of birth
            'type' => $this->faker->randomElement(['Dog', 'Cat', 'Bird', 'Fish']), // Random pet type
            'species' => $this->faker->word(),   // Random species/breed
        ];
    }
}
