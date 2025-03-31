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
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities');
            $table->integer('duration_value');
            $table->string('duration_unit');
            $table->integer('intensity');
            $table->dateTime('occurred_at');
            $table->boolean('regular_activity')->default(false);
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
