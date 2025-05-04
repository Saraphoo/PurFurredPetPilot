<?php

namespace App\AI;

use Illuminate\Support\Facades\Http;
use App\Models\Pet;

class Chat
{
    protected array $messages = [];
    protected ?Pet $pet = null;

    public function setPetContext(?Pet $pet): static
    {
        $this->pet = $pet;
        return $this;
    }

    public function systemMessage(string $message): static
    {
        $context = $message;
        
        if ($this->pet) {
            $context .= "\n\nPet Context:\n";
            $context .= "Name: {$this->pet->name}\n";
            $context .= "Type: {$this->pet->type}\n";
            if ($this->pet->species) $context .= "Species: {$this->pet->species}\n";
            if ($this->pet->breed) $context .= "Breed: {$this->pet->breed}\n";
            if ($this->pet->DOB) $context .= "Age: " . $this->pet->DOB->age . " years\n";
            if ($this->pet->weight) $context .= "Weight: {$this->pet->weight}\n";
            
            // Add pet info if available
            if ($this->pet->petInfo) {
                $context .= "\nAdditional Information:\n";
                $context .= "Diet: {$this->pet->petInfo->diet}\n";
                $context .= "Exercise: {$this->pet->petInfo->exercise}\n";
                $context .= "Medical History: {$this->pet->petInfo->medical_history}\n";
            }
        }

        $this->messages[] = [
            'role' => 'system',
            'content' => $context,
        ];

        return $this;
    }

    public function send(string $message): ?string
    {
        $this->messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        $response = Http::withToken(config('services.openai.secret'))
            ->post('https://api.openai.com/v1/chat/completions', [
                "model" => "gpt-4o-mini-search-preview",
                "messages" => $this->messages,
                "max_tokens" => 500,
            ])->json('choices.0.message.content');

        if($response){
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
            
            return $response;
        }

        return null;
    }

    public function messages()
    {
        return $this->messages;
    }
}
