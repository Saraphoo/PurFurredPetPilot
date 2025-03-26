<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Assistants\AssistantResponse;

class PetPilotAssistant
{
    protected AssistantResponse $assistant;
    public function __construct(string $assistantId){
        $this->assistant = OpenAI::assistants()->retrieve($assistantId);
    }

    public function getId(): string
    {
        return $this->assistant->id;
    }
    public static function create(array $config = []): static //this method will create our assistant and only needs to happen once
    {
        $assistant = OpenAI::assistants()->create(array_merge_recursive([
            'model'=> 'gpt-4o-mini',
            'name' => 'PetPilot',
            'instructions' => 'This assistant will help you with your pet',
            'tools' => [
                ['type'=>'file_search']
            ],
        ], $config), [
            'headers' => [
                'OpenAI-Organization' => config('services.openai.organization')
            ]
        ]);

        return new static($assistant->id);
    }

    public function acceptFile(string $file, $assistant = null): static
    {
        $file = OpenAI::files()->upload([
            'purpose' => 'assistants',
            'file' => fopen($file, 'rb')
        ]);

        if($assistant){
            OpenAI::assistants()->files()->create($assistant->id, ['file_id'=> $file->id]);
        }

        return $this;

    }

    public function createThread(){

}

    public function runThread(){

    }

}
