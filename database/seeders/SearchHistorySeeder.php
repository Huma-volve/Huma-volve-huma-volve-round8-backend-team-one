<?php

namespace Database\Seeders;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SearchHistorySeeder extends Seeder
{
    public function run(): void
    {
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

        $filterOptions = [
            ['location' => 'Cairo', 'min_rating' => 4, 'max_price' => 200],
            ['location' => 'Alexandria', 'min_rating' => 3, 'max_price' => 150],
            ['location' => null, 'min_rating' => 5, 'max_price' => null],
            ['location' => 'Giza', 'min_rating' => null, 'max_price' => 250],
            ['location' => null, 'min_rating' => null, 'max_price' => 300],
        ];

        foreach ($users as $userIndex => $user) {
            // Each patient has 1-3 search history entries based on user index
            $searchCount = ($userIndex % 3) + 1;

            foreach (range(1, $searchCount) as $index) {
                $keywordIndex = ($userIndex + $index) % count($keywords);
                $filterIndex = ($userIndex + $index) % count($filterOptions);

                SearchHistory::create([
                    'user_id' => $user->id,
                    'keyword' => $keywords[$keywordIndex],
                    'filters' => json_encode($filterOptions[$filterIndex]),
                ]);
            }
        }
    }
}
