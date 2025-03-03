<?php

namespace Database\Seeders;

use App\Models\PetInfo;
use Illuminate\Database\Seeder;

class PetInfoSeeder extends Seeder
{
    public function run()
    {
        PetInfo::create([
            'pet_id' => 1,
            'habitat_size' => 'Large yard',
            'exercise_needs' => 'High',
            'daily_supplements' => 'Multivitamin',
            'food_schedule' => 'Twice a day',
        ]);

        PetInfo::create([
            'pet_id' => 2,
            'habitat_size' => 'Indoor space',
            'exercise_needs' => 'Moderate',
            'food_schedule' => 'free feeding',
        ]);
    }
}
