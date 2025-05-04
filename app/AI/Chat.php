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
        $context = "You are an AI assistant powered by OpenAI's GPT model with web search capabilities. Follow these rules strictly:
1. ALWAYS use web search to find current information before responding
2. ALWAYS cite sources using this exact format: [Source: website name - URL]
3. Keep responses concise and focused on the specific question asked
4. Be transparent about being an AI
5. If you can't find a reliable source, say 'I couldn't find a reliable source for this information'
6. Never make up information or pretend to have personal experiences
7. If the response would be too long, focus on the most important points
8. For every response, you MUST include at least one source citation

Example format:
'Cats are obligate carnivores. [Source: ASPCA - https://www.aspca.org/pet-care/cat-care/cat-nutrition-tips]'

" . $message;
        
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
        try {
            $this->messages[] = [
                'role' => 'user',
                'content' => $message,
            ];

            $apiKey = config('services.openai.secret');
            \Log::info('OpenAI Configuration', [
                'api_key_exists' => !empty($apiKey),
                'api_key_length' => strlen($apiKey),
                'api_key_prefix' => substr($apiKey, 0, 7) . '...'
            ]);

            \Log::info('Sending message to OpenAI', [
                'messages' => $this->messages
            ]);

            $requestData = [
                "model" => "gpt-4o-mini-search-preview",
                "messages" => $this->messages,
                "max_tokens" => 500
            ];

            \Log::info('Request data', ['data' => $requestData]);

            $response = Http::withToken($apiKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post('https://api.openai.com/v1/chat/completions', $requestData);

            \Log::info('OpenAI response received', [
                'status' => $response->status(),
                'body' => $response->json(),
                'raw_body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                \Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'raw_body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                throw new \Exception('OpenAI API request failed: ' . $response->body());
            }

            $content = $response->json('choices.0.message.content');

            if (!$content) {
                \Log::error('No content in OpenAI response', [
                    'response' => $response->json(),
                    'raw_body' => $response->body()
                ]);
                throw new \Exception('No content in OpenAI response');
            }

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $content,
            ];
            
            return $content;
        } catch (\Exception $e) {
            \Log::error('Error in Chat::send', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);
            throw $e;
        }
    }

    public function messages()
    {
        return $this->messages;
    }
}
