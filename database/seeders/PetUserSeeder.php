<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PetUserSeeder extends Seeder
{
    public function run()
    {
        // Get the first user
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Assign Buddy to the user
        DB::table('pet_user')->insert([
            'user_id' => $user->id,
            'pet_id' => 1,
        ]);

        // Assign Whiskers to the user
        DB::table('pet_user')->insert([
            'user_id' => $user->id,
            'pet_id' => 2,
        ]);
    }
}
