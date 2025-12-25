<?php

namespace Database\Seeders;

use App\Models\SavedCard;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SavedCardSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('user_type', ['patient', 'doctor'])->get();

        $brands = ['Visa', 'Mastercard', 'American Express'];
        $lastFours = ['1234', '5678', '9012', '3456', '7890'];
        $expYears = [2025, 2026, 2027, 2028, 2029, 2030];

        foreach ($users as $userIndex => $user) {
            // 50% of users have saved cards (based on even/odd index)
            if ($userIndex % 2 === 0) {
                // 1-2 cards per user
                $cardCount = ($userIndex % 2) + 1;

                foreach (range(1, $cardCount) as $index) {
                    SavedCard::create([
                        'user_id' => $user->id,
                        'provider_token' => Str::uuid()->toString(),
                        'brand' => $brands[($userIndex + $index) % count($brands)],
                        'last_four' => $lastFours[($userIndex + $index) % count($lastFours)],
                        'exp_month' => (($userIndex + $index) % 12) + 1,
                        'exp_year' => $expYears[($userIndex + $index) % count($expYears)],
                        'is_default' => $index === 1, // First card is default
                    ]);
                }
            }
        }
    }
}
