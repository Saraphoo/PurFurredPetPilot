<?php

namespace App\AI;

use Illuminate\Support\Facades\Http;

class Chat
{
    protected array $messages = [];
    protected array $lastAnnotations = [];

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
                "max_tokens" => 1000,
                "temperature" => 0.7,
                "presence_penalty" => 0.6,
                "response_format" => [
                    "type" => "json_object",
                    "schema" => [
                        "type" => "object",
                        "properties" => [
                            "response" => [
                                "type" => "string",
                                "description" => "The main response text"
                            ],
                            "annotations" => [
                                "type" => "array",
                                "items" => [
                                    "type" => "object",
                                    "properties" => [
                                        "text" => ["type" => "string"],
                                        "type" => ["type" => "string"],
                                        "confidence" => ["type" => "number"]
                                    ]
                                ]
                            ]
                        ],
                        "required" => ["response", "annotations"]
                    ]
                ]
            ])->json('choices.0.message.content');

        if($response){
            $parsedResponse = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($parsedResponse['response'])) {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => $parsedResponse['response'],
                ];
                
                $this->lastAnnotations = $parsedResponse['annotations'] ?? [];
                
                return $parsedResponse['response'];
            }
            
            // Fallback for non-JSON responses or invalid format
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

    public function getLastAnnotations(): array
    {
        return $this->lastAnnotations;
    }
}
