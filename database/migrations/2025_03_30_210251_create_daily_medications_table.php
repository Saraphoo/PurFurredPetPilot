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
        Schema::create('daily_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('medications');
            $table->dateTime('given_at');
            $table->integer('dosage_given')->default(0);
            $table->string('reason_given')->nullable();
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_medications');
    }
};
