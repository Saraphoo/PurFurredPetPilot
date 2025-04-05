<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->text('response');
            $table->vector('embedding', 1536); // OpenAI embeddings are 1536-dimensional
            $table->timestamps();
        });

        // Create an index for vector similarity search
        DB::statement('CREATE INDEX chat_sessions_embedding_idx ON chat_sessions USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
    }

    public function down()
    {
        Schema::dropIfExists('chat_sessions');
    }
}; 