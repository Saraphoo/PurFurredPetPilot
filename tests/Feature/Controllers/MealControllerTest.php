<?php

namespace Tests\Feature\Controllers;

use App\Models\Meal;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealControllerTest extends TestCase
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
    public function it_lists_meals_for_a_pet()
    {
        Meal::create([
            'pet_id' => $this->pet->id,
            'feed_time' => '08:00',
            'name' => 'Chicken & Rice',
            'brand' => 'Royal Canin',
            'meal_type' => 'Dry Kibble',
            'portion_size' => '1 cup',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('meals.index', ['pet' => $this->pet->id]));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Chicken & Rice']);
    }

    /** @test */
    public function it_can_store_a_meal_schedule()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('meals.store', ['pet' => $this->pet->id]), [
                'feed_time' => '08:00',
                'name' => 'Chicken & Rice',
                'brand' => 'Royal Canin',
                'meal_type' => 'Dry Kibble',
                'portion_size' => '1 cup',
                'notes' => 'Morning feeding'
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('meals', [
            'pet_id' => $this->pet->id,
            'name' => 'Chicken & Rice',
            'brand' => 'Royal Canin',
            'meal_type' => 'Dry Kibble',
            'portion_size' => '1 cup',
            'notes' => 'Morning feeding'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('meals.store', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['feed_time', 'name', 'brand', 'meal_type', 'portion_size']);
    }

    /** @test */
    public function it_can_update_a_meal_schedule()
    {
        $meal = Meal::create([
            'pet_id' => $this->pet->id,
            'feed_time' => '08:00',
            'name' => 'Old Food',
            'brand' => 'Old Brand',
            'meal_type' => 'Dry Kibble',
            'portion_size' => '1 cup',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('meals.update', ['pet' => $this->pet->id, 'meal' => $meal->id]), [
                'feed_time' => '18:00',
                'name' => 'New Food',
                'brand' => 'New Brand',
                'meal_type' => 'Canned',
                'portion_size' => '2 cups',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('meals', [
            'id' => $meal->id,
            'name' => 'New Food',
            'brand' => 'New Brand',
            'meal_type' => 'Canned',
            'portion_size' => '2 cups',
        ]);
    }

    /** @test */
    public function it_can_delete_a_meal_schedule()
    {
        $meal = Meal::create([
            'pet_id' => $this->pet->id,
            'feed_time' => '08:00',
            'name' => 'Food',
            'brand' => 'Brand',
            'meal_type' => 'Dry Kibble',
            'portion_size' => '1 cup',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('meals.destroy', ['pet' => $this->pet->id, 'meal' => $meal->id]));

        $response->assertOk();

        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }

    /** @test */
    public function it_can_log_a_daily_meal()
    {
        $meal = Meal::create([
            'pet_id' => $this->pet->id,
            'feed_time' => '08:00',
            'name' => 'Food',
            'brand' => 'Brand',
            'meal_type' => 'Dry Kibble',
            'portion_size' => '1 cup',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('meals.log', ['pet' => $this->pet->id]), [
                'meal_id' => $meal->id,
                'fed_at' => '2024-01-01 08:00:00',
                'portions_fed' => 1,
                'notes' => 'Ate everything'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('daily_meals', [
            'meal_id' => $meal->id,
            'portions_fed' => 1,
            'notes' => 'Ate everything'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_logging_a_daily_meal()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('meals.log', ['pet' => $this->pet->id]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['meal_id', 'fed_at', 'portions_fed']);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_store_a_meal()
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('meals.store', ['pet' => $this->pet->id]), [
                'feed_time' => '08:00',
                'name' => 'Chicken & Rice',
                'brand' => 'Royal Canin',
                'meal_type' => 'Dry Kibble',
                'portion_size' => '1 cup',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('meals', ['pet_id' => $this->pet->id]);
    }

    /** @test */
    public function a_user_who_is_not_a_caretaker_cannot_list_meals()
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('meals.index', ['pet' => $this->pet->id]));

        $response->assertForbidden();
    }
}
