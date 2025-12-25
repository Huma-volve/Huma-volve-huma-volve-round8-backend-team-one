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
            'license_number' => $this->faker->numerify('LIC-#####'),
            'bio' => $this->faker->paragraph(),
            'session_price' => $this->faker->randomFloat(2, 50, 500),
            'clinic_address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'experience_length' => $this->faker->numberBetween(1, 30),
        ];
    }
}
