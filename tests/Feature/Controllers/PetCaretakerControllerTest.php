<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetCaretakerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->pet = Pet::factory()->ownedBy($this->owner)->create();
    }

    /** @test */
    public function any_caretaker_can_list_caretakers()
    {
        $caretaker = User::factory()->create();
        $this->pet->users()->attach($caretaker->id, ['role' => Pet::ROLE_CARETAKER]);

        $response = $this->actingAs($caretaker)->getJson(route('caretakers.index', ['pet' => $this->pet->id]));

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    /** @test */
    public function a_non_caretaker_cannot_list_caretakers()
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('caretakers.index', ['pet' => $this->pet->id]));

        $response->assertForbidden();
    }

    /** @test */
    public function the_owner_can_add_a_caretaker()
    {
        $newCaretaker = User::factory()->create();

        $response = $this->actingAs($this->owner)->post(route('caretakers.store', ['pet' => $this->pet->id]), [
            'email' => $newCaretaker->email,
            'role' => Pet::ROLE_CARETAKER,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $newCaretaker->id,
            'role' => Pet::ROLE_CARETAKER,
        ]);
    }

    /** @test */
    public function the_owner_can_add_a_second_owner()
    {
        $newOwner = User::factory()->create();

        $response = $this->actingAs($this->owner)->post(route('caretakers.store', ['pet' => $this->pet->id]), [
            'email' => $newOwner->email,
            'role' => Pet::ROLE_OWNER,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $newOwner->id,
            'role' => Pet::ROLE_OWNER,
        ]);
    }

    /** @test */
    public function a_non_owner_caretaker_cannot_add_a_caretaker()
    {
        $caretaker = User::factory()->create();
        $this->pet->users()->attach($caretaker->id, ['role' => Pet::ROLE_CARETAKER]);

        $response = $this->actingAs($caretaker)->post(route('caretakers.store', ['pet' => $this->pet->id]), [
            'email' => User::factory()->create()->email,
            'role' => Pet::ROLE_CARETAKER,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function it_cannot_add_the_same_caretaker_twice()
    {
        $caretaker = User::factory()->create();
        $this->pet->users()->attach($caretaker->id, ['role' => Pet::ROLE_CARETAKER]);

        $response = $this->actingAs($this->owner)->post(route('caretakers.store', ['pet' => $this->pet->id]), [
            'email' => $caretaker->email,
            'role' => Pet::ROLE_CARETAKER,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function the_owner_can_change_a_caretakers_role()
    {
        $caretaker = User::factory()->create();
        $this->pet->users()->attach($caretaker->id, ['role' => Pet::ROLE_CARETAKER]);

        $response = $this->actingAs($this->owner)
            ->put(route('caretakers.update', ['pet' => $this->pet->id, 'user' => $caretaker->id]), [
                'role' => Pet::ROLE_OWNER,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $caretaker->id,
            'role' => Pet::ROLE_OWNER,
        ]);
    }

    /** @test */
    public function the_sole_owner_cannot_be_demoted()
    {
        $response = $this->actingAs($this->owner)
            ->put(route('caretakers.update', ['pet' => $this->pet->id, 'user' => $this->owner->id]), [
                'role' => Pet::ROLE_CARETAKER,
            ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $this->owner->id,
            'role' => Pet::ROLE_OWNER,
        ]);
    }

    /** @test */
    public function the_owner_can_remove_a_caretaker()
    {
        $caretaker = User::factory()->create();
        $this->pet->users()->attach($caretaker->id, ['role' => Pet::ROLE_CARETAKER]);

        $response = $this->actingAs($this->owner)
            ->delete(route('caretakers.destroy', ['pet' => $this->pet->id, 'user' => $caretaker->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $caretaker->id,
        ]);
    }

    /** @test */
    public function the_sole_owner_cannot_be_removed()
    {
        $response = $this->actingAs($this->owner)
            ->delete(route('caretakers.destroy', ['pet' => $this->pet->id, 'user' => $this->owner->id]));

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $this->owner->id,
        ]);
    }

    /** @test */
    public function an_owner_can_be_removed_if_another_owner_remains()
    {
        $secondOwner = User::factory()->create();
        $this->pet->users()->attach($secondOwner->id, ['role' => Pet::ROLE_OWNER]);

        $response = $this->actingAs($this->owner)
            ->delete(route('caretakers.destroy', ['pet' => $this->pet->id, 'user' => $this->owner->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('pet_user', [
            'pet_id' => $this->pet->id,
            'user_id' => $this->owner->id,
        ]);
    }
}
