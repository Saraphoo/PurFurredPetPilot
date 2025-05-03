<?php

namespace App\AI;

use Illuminate\Support\Facades\Http;

class Chat
{
    protected array $messages = [];

    public function systemMessage(string $message): static
    {
        $this->messages[] = [
            'role' => 'system',
            'content' => $message,
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
