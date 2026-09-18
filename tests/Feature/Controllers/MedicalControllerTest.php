<?php

namespace Tests\Feature\Controllers;

use App\Models\Medication;
use App\Models\Pet;
use App\Models\SpecialNeed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalControllerTest extends TestCase
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
    public function it_can_store_medical_information()
    {
        $specialNeeds = [
            [
                'name' => 'Diabetes',
                'affects' => 'Diet',
                'notes' => 'Requires insulin'
            ]
        ];

        $medications = [
            [
                'name' => 'Insulin',
                'prescribed_on' => '2024-01-01',
                'notes' => 'Daily injection'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('medical.store', ['pet' => $this->pet->id]), [
                'special_needs' => $specialNeeds,
                'medications' => $medications,
                'notes' => 'General medical notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('special_needs', [
            'pet_id' => $this->pet->id,
            'name' => 'Diabetes',
            'affects' => 'Diet',
            'notes' => 'Requires insulin'
        ]);

        $this->assertDatabaseHas('medications', [
            'pet_id' => $this->pet->id,
            'name' => 'Insulin',
            'prescribed_on' => '2024-01-01',
            'notes' => 'Daily injection'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->post(route('medical.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['special_needs', 'medications']);
    }

    /** @test */
    public function it_can_update_medical_information()
    {
        SpecialNeed::create([
            'pet_id' => $this->pet->id,
            'name' => 'Old Condition',
            'affects' => 'Old Affect',
            'notes' => 'Old notes'
        ]);

        $newSpecialNeeds = [
            [
                'name' => 'New Condition',
                'affects' => 'New Affect',
                'notes' => 'New notes'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->put(route('medical.update', ['pet' => $this->pet->id]), [
                'special_needs' => $newSpecialNeeds,
                'medications' => [],
                'notes' => 'Updated notes'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('special_needs', [
            'pet_id' => $this->pet->id,
            'name' => 'Old Condition'
        ]);

        $this->assertDatabaseHas('special_needs', [
            'pet_id' => $this->pet->id,
            'name' => 'New Condition',
            'affects' => 'New Affect',
            'notes' => 'New notes'
        ]);
    }

    /** @test */
    public function it_can_log_daily_medication()
    {
        $medication = Medication::create([
            'pet_id' => $this->pet->id,
            'name' => 'Test Medication',
            'prescribed_on' => '2024-01-01',
            'notes' => 'Test notes'
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('medical.log', ['pet' => $this->pet->id]), [
                'medication_id' => $medication->id,
                'given_at' => '2024-01-01 08:00:00',
                'dosage_given' => 1,
                'notes' => 'Administered as prescribed'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('daily_medications', [
            'medication_id' => $medication->id,
            'dosage_given' => 1,
            'notes' => 'Administered as prescribed'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_logging_daily_medication()
    {
        $response = $this->actingAs($this->user)
            ->post(route('medical.log', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['medication_id', 'given_at', 'dosage_given']);
    }
}
