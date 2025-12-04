<?php

namespace Database\Seeders;

use App\Models\SavedCard;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SavedCardSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::whereIn('user_type', ['patient', 'doctor'])->get();

        foreach ($users as $user) {
            // 50% chance to have saved cards
            if ($faker->boolean(50)) {
                $cardCount = $faker->numberBetween(1, 3);

                foreach (range(1, $cardCount) as $index) {
                    SavedCard::create([
                        'user_id' => $user->id,
                        'provider_token' => $faker->uuid,
                        'brand' => $faker->randomElement(['Visa', 'Mastercard', 'American Express']),
                        'last_four' => $faker->numerify('####'),
                        'exp_month' => $faker->numberBetween(1, 12),
                        'exp_year' => $faker->numberBetween(2024, 2030),
                        'is_default' => $index === 1, // First card is default
                    ]);
                }
            }
        }
    }
}
