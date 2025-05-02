<?php

namespace App\Services;

use OpenAI\Client;

class OpenAIService
{
    private Client $client;
    private string $model;

    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.secret'));
        $this->model = 'text-embedding-3-small'; // or text-embedding-3-large for better quality
    }

    public function getEmbedding(string $text): array
    {
        if (app()->environment('testing')) {
            // Return a mock embedding for testing
            return array_fill(0, 1536, 0.1);
        }

        $response = $this->client->embeddings()->create([
            'model' => $this->model,
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }

    public function getModel(): string
    {
        return $this->model;
    }
} 