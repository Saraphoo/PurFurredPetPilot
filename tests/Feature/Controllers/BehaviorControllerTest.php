<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Behavior;
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
        $this->pet = Pet::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_can_store_behavior_information()
    {
        $behaviors = [
            'Biting',
            'Kicking',
            'Spinning'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('behaviors.store', ['pet' => $this->pet->id]), [
                'behaviors' => $behaviors,
                'behavior_notes' => 'Behavior notes',
                'general_notes' => 'General notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert behaviors were created
        foreach ($behaviors as $behavior) {
            $this->assertDatabaseHas('behaviors', [
                'pet_id' => $this->pet->id,
                'name' => $behavior,
                'behavior_notes' => 'Behavior notes',
                'general_notes' => 'General notes'
            ]);
        }
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
        // Create initial data
        Behavior::create([
            'pet_id' => $this->pet->id,
            'name' => 'Old Behavior',
            'behavior_notes' => 'Old behavior notes',
            'general_notes' => 'Old general notes'
        ]);

        $newBehaviors = [
            'New Behavior 1',
            'New Behavior 2'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('behaviors.update', ['pet' => $this->pet->id]), [
                'behaviors' => $newBehaviors,
                'behavior_notes' => 'New behavior notes',
                'general_notes' => 'New general notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert old behavior was deleted
        $this->assertDatabaseMissing('behaviors', [
            'pet_id' => $this->pet->id,
            'name' => 'Old Behavior'
        ]);

        // Assert new behaviors were created
        foreach ($newBehaviors as $behavior) {
            $this->assertDatabaseHas('behaviors', [
                'pet_id' => $this->pet->id,
                'name' => $behavior,
                'behavior_notes' => 'New behavior notes',
                'general_notes' => 'New general notes'
            ]);
        }
    }
} 