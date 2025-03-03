<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetUserSeeder extends Seeder
{
    public function run()
    {
        // Assign Buddy to John Doe (user ID 1)
        DB::table('pet_user')->insert([
            'user_id' => 1,
            'pet_id' => 1,
        ]);

        // Assign Whiskers to John Doe (user ID 1)
        DB::table('pet_user')->insert([
            'user_id' => 1,
            'pet_id' => 2,
        ]);
    }
}
