<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\User;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorProfileFactory extends Factory
{
    protected $model = DoctorProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialty_id' => Speciality::factory(),
            'license_number' => $this->faker->unique()->numerify('LIC-#####'),
            'bio' => $this->faker->paragraph,
            'session_price' => $this->faker->randomFloat(2, 50, 500),
            'experience_length' => $this->faker->numberBetween(1, 20),
            'rating_avg' => $this->faker->randomFloat(1, 1, 5),
            'clinic_address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'is_approved' => true,
        ];
    }
}
