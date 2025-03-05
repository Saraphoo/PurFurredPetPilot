<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PetInfo;

class PetInfoSeeder extends Seeder
{
    public function run()
    {
        // For Pet 1
        $pet1Attributes = [
            'habitat_size' => 'Large yard',
            'exercise_needs' => 'High',
            'daily_supplements' => 'Multivitamin',
            'food_schedule' => 'Twice a day',
        ];

        foreach ($pet1Attributes as $key => $value) {
            PetInfo::create([
                'pet_id' => 1,
                'key' => $key,
                'value' => $value,
            ]);
        }

        // For Pet 2
        $pet2Attributes = [
            'habitat_size' => 'Indoor space',
            'exercise_needs' => 'Moderate',
            'food_schedule' => 'free feeding',
        ];

        foreach ($pet2Attributes as $key => $value) {
            PetInfo::create([
                'pet_id' => 2,
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
