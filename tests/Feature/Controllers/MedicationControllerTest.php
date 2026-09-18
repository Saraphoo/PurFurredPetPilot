<?php

namespace Tests\Feature\Controllers;

use App\Models\Medication;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationControllerTest extends TestCase
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
    public function it_can_store_a_medication()
    {
        $response = $this->actingAs($this->user)
            ->post(route('medications.store', ['pet' => $this->pet->id]), [
                'name' => 'Apoquel',
                'medication_type' => 'Tablet',
                'dosage' => '16mg',
                'prescribed_on' => '2024-01-01',
                'frequency_value' => 2,
                'frequency_unit' => 'day',
                'notes' => 'Take with food'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medications', [
            'pet_id' => $this->pet->id,
            'name' => 'Apoquel',
            'medication_type' => 'Tablet',
            'dosage' => '16mg',
            'frequency_value' => 2,
            'frequency_unit' => 'day',
            'notes' => 'Take with food'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->post(route('medications.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_can_update_a_medication()
    {
        $medication = Medication::create([
            'pet_id' => $this->pet->id,
            'name' => 'Old Medication',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('medications.update', ['pet' => $this->pet->id, 'medication' => $medication->id]), [
                'name' => 'New Medication',
                'dosage' => '10mg',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medications', [
            'id' => $medication->id,
            'name' => 'New Medication',
            'dosage' => '10mg',
        ]);
    }

    /** @test */
    public function it_can_delete_a_medication()
    {
        $medication = Medication::create([
            'pet_id' => $this->pet->id,
            'name' => 'Medication',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('medications.destroy', ['pet' => $this->pet->id, 'medication' => $medication->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('medications', ['id' => $medication->id]);
    }
}
