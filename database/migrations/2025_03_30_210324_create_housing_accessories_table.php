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
        Schema::create('housing_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('housing_id')->constrained('housings')->onDelete('cascade');
            $table->string('accessory_type');
            $table->string('name')->nullable();
            $table->string('brand')->nullable();
            $table->string('accessory_size')->nullable();
            $table->string('material')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housing_accessories');
    }
};
