<?php

namespace Database\Factories;

use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorProfile>
 */
class DoctorProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialty_id' => Speciality::factory(),
            'license_number' => fake()->numerify('LIC-#####'),
            'bio' => fake()->paragraph(),
            'session_price' => fake()->randomFloat(2, 50, 500),
            'clinic_address' => fake()->address(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'experience_length' => fake()->numberBetween(1, 30),
        ];
    }
}
