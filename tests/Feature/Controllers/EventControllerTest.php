<?php

namespace Tests\Feature\Controllers;

use App\Models\Event;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
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
    public function it_lists_only_the_authenticated_users_events()
    {
        $otherUser = User::factory()->create();
        $otherPet = Pet::factory()->ownedBy($otherUser)->create();

        Event::create([
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        Event::create([
            'user_id' => $otherUser->id,
            'pet_id' => $otherPet->id,
            'title' => 'Someone else\'s event',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($this->user)->getJson('/events');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Vet visit']);
    }

    /** @test */
    public function it_can_create_an_event()
    {
        $response = $this->actingAs($this->user)->postJson('/events', [
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'description' => 'Annual checkup',
            'color' => 'primary',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('events', [
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
        ]);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_of_the_pet_cannot_create_an_event_for_it()
    {
        $response = $this->actingAs(User::factory()->create())->postJson('/events', [
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('events', ['pet_id' => $this->pet->id]);
    }

    /** @test */
    public function it_validates_that_end_time_is_after_start_time()
    {
        $response = $this->actingAs($this->user)->postJson('/events', [
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 10:00:00',
            'end_time' => '2024-01-01 09:00:00',
            'color' => 'primary',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_time']);
    }

    /** @test */
    public function the_owner_can_update_their_event()
    {
        $event = Event::create([
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($this->user)->putJson("/events/{$event->id}", [
            'title' => 'Updated title',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 11:00:00',
            'color' => 'primary',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated title']);
    }

    /** @test */
    public function another_user_cannot_update_someone_elses_event()
    {
        $otherUser = User::factory()->create();
        $event = Event::create([
            'user_id' => $otherUser->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($this->user)->putJson("/events/{$event->id}", [
            'title' => 'Hijacked title',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 11:00:00',
            'color' => 'primary',
            'pet_id' => $this->pet->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Vet visit']);
    }

    /** @test */
    public function the_owner_can_delete_their_event()
    {
        $event = Event::create([
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/events/{$event->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /** @test */
    public function another_user_cannot_delete_someone_elses_event()
    {
        $otherUser = User::factory()->create();
        $event = Event::create([
            'user_id' => $otherUser->id,
            'pet_id' => $this->pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/events/{$event->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
