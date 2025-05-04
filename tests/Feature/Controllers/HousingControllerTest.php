<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Housing;
use App\Models\HousingAccessory;
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
        $this->pet = Pet::factory()->create(['user_id' => $this->user->id]);
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
            ->post(route('housing.store', ['pet' => $this->pet->id]), [
                'total_space_value' => '100',
                'total_space_unit' => 'square feet',
                'housing_type' => 'Cage',
                'flooring_type' => 'Wire',
                'bedding_type' => 'Wood Shavings',
                'accessories' => $accessories,
                'notes' => 'General housing notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert housing was created
        $this->assertDatabaseHas('housings', [
            'pet_id' => $this->pet->id,
            'total_space_value' => '100',
            'total_space_unit' => 'square feet',
            'housing_type' => 'Cage',
            'flooring_type' => 'Wire',
            'bedding_type' => 'Wood Shavings',
            'notes' => 'General housing notes'
        ]);

        // Assert accessory was created
        $this->assertDatabaseHas('housing_accessories', [
            'pet_id' => $this->pet->id,
            'accessory_type' => 'House',
            'accessory_name' => 'Cozy Cave',
            'accessory_size' => 'Large',
            'accessory_brand' => 'Kaytee',
            'accessory_material' => 'Plastic',
            'accessory_notes' => 'Main sleeping area'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->post(route('housing.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors([
            'total_space_value',
            'total_space_unit',
            'housing_type',
            'flooring_type',
            'bedding_type'
        ]);
    }

    /** @test */
    public function it_can_update_housing_information()
    {
        // Create initial data
        Housing::create([
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
            ->put(route('housing.update', ['pet' => $this->pet->id]), [
                'total_space_value' => '200',
                'total_space_unit' => 'square feet',
                'housing_type' => 'New Type',
                'flooring_type' => 'New Flooring',
                'bedding_type' => 'New Bedding',
                'accessories' => $newAccessories,
                'notes' => 'Updated notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert old data was updated
        $this->assertDatabaseHas('housings', [
            'pet_id' => $this->pet->id,
            'total_space_value' => '200',
            'total_space_unit' => 'square feet',
            'housing_type' => 'New Type',
            'flooring_type' => 'New Flooring',
            'bedding_type' => 'New Bedding',
            'notes' => 'Updated notes'
        ]);

        // Assert new accessory was created
        $this->assertDatabaseHas('housing_accessories', [
            'pet_id' => $this->pet->id,
            'accessory_type' => 'Bed',
            'accessory_name' => 'New Bed',
            'accessory_size' => 'Medium',
            'accessory_brand' => 'New Brand',
            'accessory_material' => 'New Material',
            'accessory_notes' => 'New notes'
        ]);
    }
} 