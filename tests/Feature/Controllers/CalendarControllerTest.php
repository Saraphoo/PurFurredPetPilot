<?php

namespace Tests\Feature\Controllers;

use App\Models\Event;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_are_redirected_to_login()
    {
        $response = $this->get(route('calendar.index'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_shows_the_authenticated_users_events_and_pets()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->ownedBy($user)->create();
        Event::create([
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'title' => 'Vet visit',
            'start_time' => '2024-01-01 09:00:00',
            'end_time' => '2024-01-01 10:00:00',
            'color' => 'primary',
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Calendar')
            ->has('events', 1)
            ->has('pets', 1)
        );
    }
}
