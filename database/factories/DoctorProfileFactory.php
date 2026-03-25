<?php

namespace Database\Factories;

use App\Models\Speciality;
use App\Models\User;
use Faker\Factory as FakerFactory;
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
        $faker = FakerFactory::create();

        return [
            'user_id' => User::factory(),
            'specialty_id' => Speciality::factory(),
            'license_number' => $faker->numerify('LIC-#####'),
            'bio' => $faker->paragraph(),
            'session_price' => $faker->randomFloat(2, 50, 500),
            'clinic_address' => $faker->address(),
            'latitude' => $faker->latitude(),
            'longitude' => $faker->longitude(),
            'experience_length' => $faker->numberBetween(1, 30),
        ];
    }
}
