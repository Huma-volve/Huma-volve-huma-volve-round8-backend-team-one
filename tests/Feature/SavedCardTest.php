<?php

namespace Tests\Feature;

use App\Models\SavedCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_saved_card_expiry()
    {
        $user = User::factory()->create();
        $card = SavedCard::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/saved-cards/{$card->id}", [
            'exp_month' => 12,
            'exp_year' => 2030,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('saved_cards', [
            'id' => $card->id,
            'exp_month' => 12,
            'exp_year' => 2030,
        ]);
    }

    public function test_update_can_set_default()
    {
        $user = User::factory()->create();
        $card1 = SavedCard::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $card2 = SavedCard::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $response = $this->actingAs($user)->putJson("/api/saved-cards/{$card2->id}", [
            'is_default' => true,
        ]);

        $response->assertStatus(200);

        // Card 2 should be default
        $this->assertDatabaseHas('saved_cards', [
            'id' => $card2->id,
            'is_default' => true,
        ]);

        // Card 1 should NOT be default
        $this->assertDatabaseHas('saved_cards', [
            'id' => $card1->id,
            'is_default' => false,
        ]);
    }

    public function test_can_set_default_via_specific_endpoint()
    {
        $user = User::factory()->create();
        $card1 = SavedCard::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $card2 = SavedCard::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $response = $this->actingAs($user)->putJson("/api/saved-cards/{$card2->id}/default");

        $response->assertStatus(200);

        // Card 2 should be default
        $this->assertDatabaseHas('saved_cards', [
            'id' => $card2->id,
            'is_default' => true,
        ]);

        // Card 1 should NOT be default
        $this->assertDatabaseHas('saved_cards', [
            'id' => $card1->id,
            'is_default' => false,
        ]);
    }
}
