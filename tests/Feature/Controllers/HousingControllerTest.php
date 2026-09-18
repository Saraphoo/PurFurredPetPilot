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
            ->post(route('housing.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors([
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
            ->put(route('housing.update', ['pet' => $this->pet->id, 'housing' => $housing->id]), [
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
            ->delete(route('housing.destroy', ['pet' => $this->pet->id, 'housing' => $housing->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('housings', ['id' => $housing->id]);
    }
}
