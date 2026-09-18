<?php

namespace App\AI;

use App\Models\Pet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Chat
{
    protected const SYSTEM_INSTRUCTIONS = <<<'TEXT'
You are an AI pet care assistant that provides accurate, up-to-date information about pet care, health, and well-being. Follow these rules strictly:
1. ALWAYS use web search to find current information before responding
2. ALWAYS cite sources using this exact format: [Source: website name - URL]
3. Keep responses concise and focused on the specific question asked
4. Be transparent about being an AI
5. If you can't find a reliable source, say "I couldn't find a reliable source for this information"
6. Never make up information or pretend to have personal experiences
7. For every response, you MUST include at least one source citation

Example format:
"Cats are obligate carnivores. [Source: ASPCA - https://www.aspca.org/pet-care/cat-care/cat-nutrition-tips]"
TEXT;

    protected array $messages = [];
    protected ?Pet $pet = null;

    public function setPetContext(?Pet $pet): static
    {
        $this->pet = $pet;
        return $this;
    }

    public function systemMessage(): static
    {
        $context = self::SYSTEM_INSTRUCTIONS;

        if ($this->pet) {
            $context .= "\n\nPet Context:\n";
            $context .= "Name: {$this->pet->name}\n";
            $context .= "Type: {$this->pet->type}\n";
            if ($this->pet->species) $context .= "Species: {$this->pet->species}\n";
            if ($this->pet->breed) $context .= "Breed: {$this->pet->breed}\n";
            if ($this->pet->DOB) $context .= "Age: " . $this->pet->DOB->age . " years\n";
            if ($this->pet->weight) $context .= "Weight: {$this->pet->weight}\n";

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
                'model' => 'gpt-4o-mini-search-preview',
                'messages' => $this->messages,
                'max_tokens' => 500,
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('OpenAI API request failed: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        if (!$content) {
            Log::error('No content in OpenAI response', ['response' => $response->json()]);
            throw new \RuntimeException('No content in OpenAI response');
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $content,
        ];

        return $content;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
