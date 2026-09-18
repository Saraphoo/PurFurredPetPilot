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
        $this->pet = Pet::factory()->ownedBy($this->user)->create();
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

    /** @test */
    public function it_only_lists_pets_the_user_is_a_caretaker_of()
    {
        $otherUser = User::factory()->create();
        Pet::factory()->ownedBy($otherUser)->create();

        $response = $this->actingAs($this->user)->getJson('/user/pets');

        $response->assertOk();
        $response->assertJsonCount(1, 'pets');
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_chat_about_the_pet()
    {
        Http::fake();

        $response = $this->actingAs(User::factory()->create())->postJson('/chat', [
            'message' => 'Tell me about this pet',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertForbidden();
    }
}
