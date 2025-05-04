<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;
use App\Models\User;

class PetSeeder extends Seeder
{
    public function run()
    {
        // Get or create the first user
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        Pet::create([
            'name' => 'Buddy',
            'DOB' => '2019-04-12',
            'type' => 'Dog',
            'sex' => 'M',
            'breed' => 'Golden Retriever',
            'weight' => '50 lbs',
            'user_id' => $user->id
        ]);

        Pet::create([
            'name' => 'Whiskers',
            'DOB' => '2018-02-19',
            'type' => 'Cat',
            'sex' => 'F',
            'breed' => 'Domestic Shorthair',
            'weight' => '10 lbs',
            'user_id' => $user->id
        ]);
    }
}
