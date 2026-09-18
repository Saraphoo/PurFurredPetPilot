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
        $this->pet = Pet::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_can_store_a_meal_schedule()
    {
        $response = $this->actingAs($this->user)
            ->post(route('meals.store', ['pet' => $this->pet->id]), [
                'feed_time' => '08:00',
                'name' => 'Chicken & Rice',
                'brand' => 'Royal Canin',
                'meal_type' => 'Dry Kibble',
                'portion_size' => '1 cup',
                'notes' => 'Morning feeding'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

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
            ->post(route('meals.store', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['feed_time', 'name', 'brand', 'meal_type', 'portion_size']);
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
            ->put(route('meals.update', ['pet' => $this->pet->id, 'meal' => $meal->id]), [
                'feed_time' => '18:00',
                'name' => 'New Food',
                'brand' => 'New Brand',
                'meal_type' => 'Canned',
                'portion_size' => '2 cups',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

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
            ->delete(route('meals.destroy', ['pet' => $this->pet->id, 'meal' => $meal->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

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
            ->post(route('meals.log', ['pet' => $this->pet->id]), [
                'meal_id' => $meal->id,
                'fed_at' => '2024-01-01 08:00:00',
                'portions_fed' => 1,
                'notes' => 'Ate everything'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

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
            ->post(route('meals.log', ['pet' => $this->pet->id]), []);

        $response->assertSessionHasErrors(['meal_id', 'fed_at', 'portions_fed']);
    }
}
