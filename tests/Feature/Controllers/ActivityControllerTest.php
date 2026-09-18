<?php

namespace Tests\Feature\Controllers;

use App\Models\Activity;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
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
    public function it_lists_activities_for_a_pet()
    {
        Activity::create([
            'pet_id' => $this->pet->id,
            'activity' => 'Walking',
            'duration_value' => 30,
            'duration_unit' => 'minutes',
            'frequency_value' => 1,
            'frequency_unit' => 'day',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('activities.index', ['pet' => $this->pet->id]));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['activity' => 'Walking']);
    }

    /** @test */
    public function it_can_store_activities_for_a_pet()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('activities.store', ['pet' => $this->pet->id]), [
                'activities' => [
                    [
                        'name' => 'Walking',
                        'duration_value' => 30,
                        'duration_unit' => 'minutes',
                        'frequency_value' => 1,
                        'frequency_unit' => 'day',
                    ]
                ],
                'notes' => 'Loves the park'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('activities', [
            'pet_id' => $this->pet->id,
            'activity' => 'Walking',
            'duration_value' => 30,
            'duration_unit' => 'minutes',
            'frequency_value' => 1,
            'frequency_unit' => 'day',
            'notes' => 'Loves the park'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('activities.store', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['activities']);
    }

    /** @test */
    public function it_can_delete_an_activity()
    {
        $activity = Activity::create([
            'pet_id' => $this->pet->id,
            'activity' => 'Walking',
            'duration_value' => 30,
            'duration_unit' => 'minutes',
            'frequency_value' => 1,
            'frequency_unit' => 'day',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('activities.destroy', ['activity' => $activity->id]));

        $response->assertOk();
        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_list_activities()
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('activities.index', ['pet' => $this->pet->id]));

        $response->assertForbidden();
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_delete_an_activity()
    {
        $activity = Activity::create([
            'pet_id' => $this->pet->id,
            'activity' => 'Walking',
            'duration_value' => 30,
            'duration_unit' => 'minutes',
            'frequency_value' => 1,
            'frequency_unit' => 'day',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->deleteJson(route('activities.destroy', ['activity' => $activity->id]));

        $response->assertForbidden();
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'deleted_at' => null]);
    }
}
