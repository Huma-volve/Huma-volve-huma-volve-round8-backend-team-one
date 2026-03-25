<?php

namespace Database\Factories;

use App\Models\SavedCard;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedCardFactory extends Factory
{
    protected $model = SavedCard::class;

    public function definition()
    {
        $faker = FakerFactory::create();

        return [
            'user_id' => User::factory(),
            'provider_token' => 'tok_'.$faker->regexify('[a-zA-Z0-9]{24}'),
            'brand' => $faker->creditCardType,
            'last_four' => $faker->numerify('####'),
            'exp_month' => $faker->numberBetween(1, 12),
            'exp_year' => $faker->numberBetween(date('Y') + 1, date('Y') + 5),
            'is_default' => $faker->boolean(20),
        ];
    }
}
