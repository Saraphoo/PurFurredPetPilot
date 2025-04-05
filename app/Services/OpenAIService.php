<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', 'test-key');
        $this->model = 'text-embedding-3-small'; // or text-embedding-3-large for better quality
    }

    public function getEmbedding(string $text): array
    {
        if (app()->environment('testing')) {
            // Return a mock embedding for testing
            return array_fill(0, 1536, 0.1);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/embeddings', [
            'model' => $this->model,
            'input' => $text,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get embedding: ' . $response->body());
        }

        return $response->json()['data'][0]['embedding'];
    }

    public function getModel(): string
    {
        return $this->model;
    }
} 