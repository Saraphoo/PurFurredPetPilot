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
            'name' => $this->faker->firstName(),
            'DOB' => $this->faker->date(),
            'type' => $this->faker->randomElement(['Dog', 'Cat', 'Bird', 'Fish']),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'species' => $this->faker->word(),
        ];
    }
}
