<?php

namespace Database\Factories;

use App\Models\PetInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PetInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // List of sample keys for pet info
        $infoKeys = [
            'weight', 'height', 'diet', 'habitat size', 'exercise needs',
            'water temperature', 'pH level', 'vaccinations', 'grooming frequency'
        ];

        return [
            'pet_id' => \App\Models\Pet::factory(), // Create a related Pet object
            'key' => $this->faker->randomElement($infoKeys), // Random key from the list
            'value' => $this->faker->randomElement([
                '10 lbs', '2 feet', 'herbivore', '20 gallons', 'daily',
                '75°F', 'pH 7.5', 'up-to-date', 'weekly grooming'
            ]), // Random value for testing purposes
        ];
    }
}
