<?php

namespace Database\Factories;

use App\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialityFactory extends Factory
{
    protected $model = Speciality::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'image' => $this->faker->imageUrl(),
        ];
    }
}
