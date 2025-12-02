<?php

namespace Database\Seeders;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SearchHistorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::where('user_type', 'patient')->get();

        $keywords = [
            'Cardiologist',
            'Dentist',
            'Pediatrician',
            'Dermatologist',
            'General Practitioner',
            'Neurologist',
            'Orthopedist',
            'Psychiatrist',
        ];

        foreach ($users as $user) {
            // Each patient has 1-5 search history entries
            $searchCount = $faker->numberBetween(1, 5);

            foreach (range(1, $searchCount) as $index) {
                SearchHistory::create([
                    'user_id' => $user->id,
                    'keyword' => $faker->randomElement($keywords),
                    'filters' => json_encode([
                        'location' => $faker->optional()->city,
                        'min_rating' => $faker->optional()->numberBetween(3, 5),
                        'max_price' => $faker->optional()->numberBetween(100, 300),
                    ]),
                ]);
            }
        }
    }
}
