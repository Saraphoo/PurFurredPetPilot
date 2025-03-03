<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;

class PetSeeder extends Seeder
{
    public function run()
    {
        Pet::create([
            'name' => 'Buddy',
            'DOB' => '2019-04-12',
            'type' => 'Dog',
            'sex'=>'M',
            'breed'=> 'Golden Retriever',
            'weight'=> '50 lbs',
        ]);
        Pet::create([
            'name' => 'Whiskers',
            'DOB' => '2018-02-19',
            'type' => 'Cat',
            'sex'=>'F',
            'breed'=> 'Domestic Shorthair',
            'weight'=> '10 lbs',
        ]);
    }
}
