<?php

namespace App\AI;

use OpenAI\Client;
use OpenAI\Responses\Assistants\AssistantResponse;

class PetPilotAssistant
{
    protected AssistantResponse $assistant;
    protected Client $client;

    public function __construct(string $assistantId, Client $client = null)
    {
        $this->client = $client ?? \OpenAI::client(config('services.openai.secret'));
        $this->assistant = $this->client->assistants()->retrieve($assistantId);
    }

    public function getId(): string
    {
        return $this->assistant->id;
    }

    public static function create(array $config = []): static
    {
        $client = \OpenAI::client(config('services.openai.secret'));
        
        $assistant = $client->assistants()->create(array_merge_recursive([
            'model' => 'gpt-4o-mini',
            'name' => 'PetPilot',
            'instructions' => 'This assistant will help you with your pet',
            'tools' => [
                ['type' => 'file_search']
            ],
        ], $config), [
            'headers' => [
                'OpenAI-Organization' => config('services.openai.organization')
            ]
        ]);

        return new static($assistant->id, $client);
    }

    public function acceptFile(string $file, $assistant = null): static
    {
        $file = $this->client->files()->upload([
            'purpose' => 'assistants',
            'file' => fopen($file, 'rb')
        ]);

        if ($assistant) {
            $this->client->assistants()->files()->create($assistant->id, ['file_id' => $file->id]);
        }

        return $this;
    }

    public function createThread()
    {
        // Implement thread creation
    }

    public function runThread()
    {
        // Implement thread running
    }
}
