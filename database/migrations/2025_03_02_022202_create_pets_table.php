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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('DOB');
            $table->string('type');
            $table->char('sex');
            $table->string('species')->nullable();
            $table->string('breed')->nullable();
            $table->boolean('neutered')->nullable();
            $table->string('color')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
