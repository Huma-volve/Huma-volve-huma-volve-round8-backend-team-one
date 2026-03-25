<?php

namespace Database\Factories;

use App\Models\Speciality;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialityFactory extends Factory
{
    protected $model = Speciality::class;

    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'name' => $faker->word,
            'image' => $faker->imageUrl(),
        ];
    }
}
