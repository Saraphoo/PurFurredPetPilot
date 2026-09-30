<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pets')->whereNotNull('user_id')->orderBy('id')->chunk(100, function ($pets) {
            foreach ($pets as $pet) {
                $existing = DB::table('pet_user')
                    ->where('pet_id', $pet->id)
                    ->where('user_id', $pet->user_id)
                    ->first();

                if ($existing) {
                    DB::table('pet_user')
                        ->where('id', $existing->id)
                        ->update(['role' => 'owner', 'updated_at' => now()]);
                } else {
                    DB::table('pet_user')->insert([
                        'pet_id' => $pet->id,
                        'user_id' => $pet->user_id,
                        'role' => 'owner',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill is not reversible; the pets.user_id column is
        // restored by the next migration's down(), which is sufficient
        // to roll back this feature.
    }
};
