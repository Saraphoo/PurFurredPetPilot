<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['message', 'response', 'embedding']);
            
            // Add new columns
            $table->foreignId('pet_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->string('title')->after('pet_id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Remove new columns
            $table->dropForeign(['pet_id']);
            $table->dropColumn(['pet_id', 'title']);
            
            // Restore original columns
            $table->text('message')->nullable();
            $table->text('response')->nullable();
            $table->vector('embedding', 1536)->nullable();
        });
    }
}; 