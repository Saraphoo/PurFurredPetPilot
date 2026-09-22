<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function guests_cannot_view_the_create_pet_page()
    {
        $response = $this->get(route('pets.create'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_view_the_create_pet_page()
    {
        $response = $this->actingAs($this->user)->get(route('pets.create'));

        $response->assertOk();
    }

    /** @test */
    public function it_can_store_a_new_pet()
    {
        $response = $this->actingAs($this->user)->post(route('pets.store'), [
            'name' => 'Rex',
            'DOB' => '2020-01-01',
            'sex' => 'M',
            'type' => 'Dog',
            'species' => 'Labrador',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pets', [
            'name' => 'Rex',
            'type' => 'Dog',
            'sex' => 'M',
        ]);

        $pet = Pet::where('name', 'Rex')->firstOrFail();
        $this->assertDatabaseHas('pet_user', [
            'pet_id' => $pet->id,
            'user_id' => $this->user->id,
            'role' => Pet::ROLE_OWNER,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_a_pet()
    {
        $response = $this->actingAs($this->user)->post(route('pets.store'), []);

        $response->assertSessionHasErrors(['name', 'DOB', 'sex', 'type']);
    }

    /** @test */
    public function guests_cannot_store_a_pet()
    {
        $response = $this->post(route('pets.store'), [
            'name' => 'Rex',
            'DOB' => '2020-01-01',
            'sex' => 'M',
            'type' => 'Dog',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('pets', ['name' => 'Rex']);
    }

    /** @test */
    public function it_shows_a_pets_profile_with_related_data()
    {
        $pet = Pet::factory()->ownedBy($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('pet.show', ['pet' => $pet->id]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('pets/Show')
            ->where('pet.id', $pet->id)
        );
    }

    /** @test */
    public function guests_cannot_view_a_pets_profile()
    {
        $pet = Pet::factory()->ownedBy($this->user)->create();

        $response = $this->get(route('pet.show', ['pet' => $pet->id]));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_view_the_pet()
    {
        $pet = Pet::factory()->ownedBy(User::factory()->create())->create();

        $response = $this->actingAs($this->user)->get(route('pet.show', ['pet' => $pet->id]));

        $response->assertForbidden();
    }

    /** @test */
    public function the_owner_can_update_the_pet()
    {
        $pet = Pet::factory()->ownedBy($this->user)->create();

        $response = $this->actingAs($this->user)->put(route('pets.update', ['pet' => $pet->id]), [
            'name' => 'Updated Name',
            'DOB' => '2020-01-01',
            'sex' => 'M',
            'type' => 'Dog',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Updated Name']);
    }

    /** @test */
    public function a_non_owner_caretaker_cannot_update_the_pet()
    {
        $pet = Pet::factory()->caretakenBy($this->user)->create();

        $response = $this->actingAs($this->user)->put(route('pets.update', ['pet' => $pet->id]), [
            'name' => 'Hijacked Name',
            'DOB' => '2020-01-01',
            'sex' => 'M',
            'type' => 'Dog',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('pets', ['id' => $pet->id, 'name' => 'Hijacked Name']);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_update_the_pet()
    {
        $pet = Pet::factory()->ownedBy(User::factory()->create())->create();

        $response = $this->actingAs($this->user)->put(route('pets.update', ['pet' => $pet->id]), [
            'name' => 'Hijacked Name',
            'DOB' => '2020-01-01',
            'sex' => 'M',
            'type' => 'Dog',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function the_owner_can_delete_the_pet()
    {
        $pet = Pet::factory()->ownedBy($this->user)->create();

        $response = $this->actingAs($this->user)->delete(route('pets.destroy', ['pet' => $pet->id]));

        $response->assertRedirect(route('dashboard'));
        $this->assertSoftDeleted('pets', ['id' => $pet->id]);
    }

    /** @test */
    public function a_non_owner_caretaker_cannot_delete_the_pet()
    {
        $pet = Pet::factory()->caretakenBy($this->user)->create();

        $response = $this->actingAs($this->user)->delete(route('pets.destroy', ['pet' => $pet->id]));

        $response->assertForbidden();
        $this->assertDatabaseHas('pets', ['id' => $pet->id]);
    }
}
