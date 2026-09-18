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
        $this->pet = Pet::factory()->ownedBy($this->user)->create();
    }

    /** @test */
    public function it_lists_special_needs_and_medications_for_a_pet()
    {
        SpecialNeed::create(['pet_id' => $this->pet->id, 'name' => 'Diabetes', 'affects' => 'Diet']);
        Medication::create(['pet_id' => $this->pet->id, 'name' => 'Insulin']);

        $response = $this->actingAs($this->user)
            ->getJson(route('medical.index', ['pet' => $this->pet->id]));

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Diabetes']);
        $response->assertJsonFragment(['name' => 'Insulin']);
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
            ->postJson(route('medical.store', ['pet' => $this->pet->id]), [
                'special_needs' => $specialNeeds,
                'medications' => $medications,
            ]);

        $response->assertOk();

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
            ->postJson(route('medical.store', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['special_needs', 'medications']);
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
            ->putJson(route('medical.update', ['pet' => $this->pet->id]), [
                'special_needs' => $newSpecialNeeds,
                'medications' => [],
            ]);

        $response->assertOk();

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
            ->postJson(route('medical.log', ['pet' => $this->pet->id]), [
                'medication_id' => $medication->id,
                'given_at' => '2024-01-01 08:00:00',
                'dosage_given' => 1,
                'notes' => 'Administered as prescribed'
            ]);

        $response->assertOk();

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
            ->postJson(route('medical.log', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['medication_id', 'given_at', 'dosage_given']);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_store_medical_information()
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('medical.store', ['pet' => $this->pet->id]), [
                'special_needs' => [],
                'medications' => [],
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_view_medical_information()
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('medical.index', ['pet' => $this->pet->id]));

        $response->assertForbidden();
    }
}
