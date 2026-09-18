<?php

namespace Tests\Feature\Controllers;

use App\Models\ChatSession;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->pet = Pet::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_lists_the_authenticated_users_pets_as_json()
    {
        $response = $this->actingAs($this->user)->getJson('/user/pets');

        $response->assertOk();
        $response->assertJsonFragment(['name' => $this->pet->name]);
    }

    /** @test */
    public function it_sends_a_message_and_stores_the_conversation()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Cats need fresh water daily.']]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/chat', [
            'message' => 'How much water does my cat need?',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Cats need fresh water daily.']);

        $this->assertDatabaseHas('chat_sessions', [
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
        ]);

        $chatSession = ChatSession::where('user_id', $this->user->id)->firstOrFail();
        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $chatSession->id,
            'role' => 'user',
            'content' => 'How much water does my cat need?',
        ]);
        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $chatSession->id,
            'role' => 'assistant',
            'content' => 'Cats need fresh water daily.',
        ]);
    }

    /** @test */
    public function it_validates_the_message_is_required()
    {
        $response = $this->actingAs($this->user)->postJson('/chat', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function it_returns_an_error_when_the_pet_does_not_exist()
    {
        Http::fake();

        $response = $this->actingAs($this->user)->postJson('/chat', [
            'message' => 'Hello',
            'pet_id' => $this->pet->id + 999,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_an_error_when_the_ai_service_fails()
    {
        Http::fake([
            'api.openai.com/*' => Http::response('Service unavailable', 500),
        ]);

        $response = $this->actingAs($this->user)->postJson('/chat', [
            'message' => 'How much water does my cat need?',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertStatus(500);
        $response->assertJsonStructure(['error']);
    }
}
