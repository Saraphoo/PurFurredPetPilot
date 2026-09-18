<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pet_user', function (Blueprint $table) {
            $table->enum('role', ['owner', 'caretaker'])->default('caretaker')->after('pet_id');
            $table->unique(['pet_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_user', function (Blueprint $table) {
            $table->dropUnique(['pet_id', 'user_id']);
            $table->dropColumn('role');
        });
    }
};
