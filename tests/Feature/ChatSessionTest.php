<?php

namespace Tests\Feature;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure the testing database exists and has the vector extension
        require_once __DIR__ . '/../setup-test-database.php';
        
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_store_a_chat_session_with_embedding()
    {
        $chatSession = ChatSession::createWithEmbedding([
            'user_id' => $this->user->id,
            'message' => 'What is the best way to care for a cat?',
            'response' => 'Cats need regular feeding, grooming, and playtime. Make sure to provide fresh water and a clean litter box.'
        ]);

        $this->assertDatabaseHas('chat_sessions', [
            'user_id' => $this->user->id,
            'message' => 'What is the best way to care for a cat?',
            'response' => 'Cats need regular feeding, grooming, and playtime. Make sure to provide fresh water and a clean litter box.'
        ]);

        $this->assertNotNull($chatSession->embedding);
        $this->assertCount(1536, $chatSession->embedding);
    }

    #[Test]
    public function it_can_find_similar_chat_sessions()
    {
        // Create some test chat sessions
        $sessions = [
            [
                'message' => 'How often should I feed my cat?',
                'response' => 'Adult cats should be fed 2-3 times per day with measured portions.'
            ],
            [
                'message' => 'What is the best cat food?',
                'response' => 'High-quality commercial cat food that meets AAFCO standards is recommended.'
            ],
            [
                'message' => 'How much water does a cat need?',
                'response' => 'Cats need about 3.5-4.5 ounces of water per 5 pounds of body weight daily.'
            ]
        ];

        foreach ($sessions as $session) {
            ChatSession::createWithEmbedding([
                'user_id' => $this->user->id,
                'message' => $session['message'],
                'response' => $session['response']
            ]);
        }

        // Search for similar sessions
        $query = 'What should I feed my feline friend?';
        $similarSessions = ChatSession::findSimilar($query, 2);

        $this->assertCount(2, $similarSessions);
        $this->assertArrayHasKey('message', $similarSessions[0]);
        $this->assertArrayHasKey('response', $similarSessions[0]);
    }

    #[Test]
    public function it_returns_empty_array_when_no_similar_sessions_exist()
    {
        $query = 'What is the best way to care for a cat?';
        $similarSessions = ChatSession::findSimilar($query);

        $this->assertIsArray($similarSessions);
        $this->assertEmpty($similarSessions);
    }

    #[Test]
    public function it_only_returns_sessions_for_the_specified_limit()
    {
        // Create multiple test sessions
        for ($i = 0; $i < 10; $i++) {
            ChatSession::createWithEmbedding([
                'user_id' => $this->user->id,
                'message' => "Test message $i about cat care",
                'response' => "Test response $i about cat care"
            ]);
        }

        $query = 'Tell me about cat care';
        $similarSessions = ChatSession::findSimilar($query, 3);

        $this->assertCount(3, $similarSessions);
    }
} 