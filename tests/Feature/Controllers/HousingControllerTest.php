<?php

namespace Tests\Feature\Controllers;

use App\Models\Housing;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HousingControllerTest extends TestCase
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
    public function it_lists_housing_entries_with_accessories()
    {
        $housing = Housing::create([
            'pet_id' => $this->pet->id,
            'total_space_value' => '50',
            'total_space_unit' => 'square feet',
            'housing_type' => 'Cage',
            'flooring_type' => 'Wire',
            'bedding_type' => 'Wood Shavings',
        ]);
        $housing->accessories()->create([
            'accessory_type' => 'House',
            'name' => 'Cozy Cave',
            'accessory_size' => 'Large',
            'brand' => 'Kaytee',
            'material' => 'Plastic',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('housing.index', ['pet' => $this->pet->id]));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['housing_type' => 'Cage']);
        $response->assertJsonFragment(['name' => 'Cozy Cave']);
    }

    /** @test */
    public function it_can_store_housing_information()
    {
        $accessories = [
            [
                'type' => 'House',
                'name' => 'Cozy Cave',
                'size' => 'Large',
                'brand' => 'Kaytee',
                'material' => 'Plastic',
                'notes' => 'Main sleeping area'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('housing.store', ['pet' => $this->pet->id]), [
                'total_space_value' => '100',
                'total_space_unit' => 'square feet',
                'housing_type' => 'Cage',
                'flooring_type' => 'Wire',
                'bedding_type' => 'Wood Shavings',
                'accessories' => $accessories,
                'notes' => 'General housing notes'
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('housings', [
            'pet_id' => $this->pet->id,
            'total_space_value' => 100,
            'total_space_unit' => 'square feet',
            'housing_type' => 'Cage',
            'flooring_type' => 'Wire',
            'bedding_type' => 'Wood Shavings',
            'notes' => 'General housing notes'
        ]);

        $this->assertDatabaseHas('housing_accessories', [
            'accessory_type' => 'House',
            'name' => 'Cozy Cave',
            'accessory_size' => 'Large',
            'brand' => 'Kaytee',
            'material' => 'Plastic',
            'notes' => 'Main sleeping area'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('housing.store', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'total_space_value',
            'total_space_unit',
            'housing_type',
            'flooring_type',
            'bedding_type',
            'accessories'
        ]);
    }

    /** @test */
    public function it_can_update_housing_information()
    {
        $housing = Housing::create([
            'pet_id' => $this->pet->id,
            'total_space_value' => '50',
            'total_space_unit' => 'square feet',
            'housing_type' => 'Old Type',
            'flooring_type' => 'Old Flooring',
            'bedding_type' => 'Old Bedding',
            'notes' => 'Old notes'
        ]);

        $newAccessories = [
            [
                'type' => 'Bed',
                'name' => 'New Bed',
                'size' => 'Medium',
                'brand' => 'New Brand',
                'material' => 'New Material',
                'notes' => 'New notes'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('housing.update', ['pet' => $this->pet->id, 'housing' => $housing->id]), [
                'total_space_value' => '200',
                'total_space_unit' => 'square feet',
                'housing_type' => 'New Type',
                'flooring_type' => 'New Flooring',
                'bedding_type' => 'New Bedding',
                'accessories' => $newAccessories,
                'notes' => 'Updated notes'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('housings', [
            'id' => $housing->id,
            'total_space_value' => 200,
            'total_space_unit' => 'square feet',
            'housing_type' => 'New Type',
            'flooring_type' => 'New Flooring',
            'bedding_type' => 'New Bedding',
            'notes' => 'Updated notes'
        ]);

        $this->assertDatabaseHas('housing_accessories', [
            'housing_id' => $housing->id,
            'accessory_type' => 'Bed',
            'name' => 'New Bed',
            'accessory_size' => 'Medium',
            'brand' => 'New Brand',
            'material' => 'New Material',
            'notes' => 'New notes'
        ]);
    }

    /** @test */
    public function it_can_delete_housing_information()
    {
        $housing = Housing::create([
            'pet_id' => $this->pet->id,
            'total_space_value' => '50',
            'total_space_unit' => 'square feet',
            'housing_type' => 'Cage',
            'flooring_type' => 'Wire',
            'bedding_type' => 'Wood Shavings',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('housing.destroy', ['pet' => $this->pet->id, 'housing' => $housing->id]));

        $response->assertOk();

        $this->assertDatabaseMissing('housings', ['id' => $housing->id]);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_store_housing_information()
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('housing.store', ['pet' => $this->pet->id]), [
                'total_space_value' => '100',
                'total_space_unit' => 'square feet',
                'housing_type' => 'Cage',
                'flooring_type' => 'Wire',
                'bedding_type' => 'Wood Shavings',
                'accessories' => [],
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('housings', ['pet_id' => $this->pet->id]);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_list_housing_information()
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('housing.index', ['pet' => $this->pet->id]));

        $response->assertForbidden();
    }
}
