<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientProfile>
 */
class PatientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'user_id' => User::factory(),
            'birthdate' => $faker->date(),
            'gender' => $faker->randomElement(['male', 'female']),
            'latitude' => $faker->latitude(),
            'longitude' => $faker->longitude(),
        ];
    }
}
