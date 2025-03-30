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
        Schema::create('housing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->string('housing_type');
            $table->string('total_indoor_space_value')->nullable();
            $table->string('total_indoor_space_unit')->nullable();
            $table->string('total_outdoor_space_value')->nullable();
            $table->string('total_outdoor_space_unit')->nullable();
            $table->string('flooring_type')->nullable();
            $table->string('substrate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housing');
    }
};
