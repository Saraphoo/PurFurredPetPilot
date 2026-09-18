<?php

namespace Tests\Feature\Controllers;

use App\Models\Behavior;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BehaviorControllerTest extends TestCase
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
    public function it_can_store_behavior_information()
    {
        $response = $this->actingAs($this->user)
            ->post(route('behaviors.store', ['pet' => $this->pet->id]), [
                'behaviors' => ['Biting', 'Kicking', 'Spinning'],
                'behavior_notes' => 'Behavior notes',
                'general_notes' => 'General notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('behaviors', [
            'pet_id' => $this->pet->id,
            'behavior_notes' => 'Behavior notes',
            'general_notes' => 'General notes'
        ]);

        $behavior = Behavior::where('pet_id', $this->pet->id)->firstOrFail();
        $this->assertEquals(['Biting', 'Kicking', 'Spinning'], $behavior->behaviors);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->post(route('behaviors.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['behaviors']);
    }

    /** @test */
    public function it_can_update_behavior_information()
    {
        Behavior::create([
            'pet_id' => $this->pet->id,
            'behaviors' => ['Old Behavior'],
            'behavior_notes' => 'Old behavior notes',
            'general_notes' => 'Old general notes'
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('behaviors.update', ['pet' => $this->pet->id]), [
                'behaviors' => ['New Behavior 1', 'New Behavior 2'],
                'behavior_notes' => 'New behavior notes',
                'general_notes' => 'New general notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('behaviors', [
            'pet_id' => $this->pet->id,
            'behavior_notes' => 'Old behavior notes',
        ]);

        $behavior = Behavior::where('pet_id', $this->pet->id)->firstOrFail();
        $this->assertEquals(['New Behavior 1', 'New Behavior 2'], $behavior->behaviors);
        $this->assertEquals('New behavior notes', $behavior->behavior_notes);
        $this->assertEquals('New general notes', $behavior->general_notes);
    }

    /** @test */
    public function it_validates_required_fields_when_updating()
    {
        $response = $this->actingAs($this->user)
            ->put(route('behaviors.update', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['behaviors']);
    }

    /** @test */
    public function it_can_log_a_daily_behavior()
    {
        $behavior = Behavior::create([
            'pet_id' => $this->pet->id,
            'behaviors' => ['Barking'],
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('behaviors.log', ['pet' => $this->pet->id]), [
                'occurred_at' => '2024-01-01 08:00:00',
                'notes' => 'Barked at the mail carrier'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('daily_behaviors', [
            'behavior_id' => $behavior->id,
            'notes' => 'Barked at the mail carrier'
        ]);
    }

    /** @test */
    public function it_requires_a_behavior_record_to_exist_before_logging()
    {
        $response = $this->actingAs($this->user)
            ->post(route('behaviors.log', ['pet' => $this->pet->id]), [
                'occurred_at' => '2024-01-01 08:00:00',
            ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('daily_behaviors', 0);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_store_behavior_information()
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('behaviors.store', ['pet' => $this->pet->id]), [
                'behaviors' => ['Biting'],
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('behaviors', ['pet_id' => $this->pet->id]);
    }
}
