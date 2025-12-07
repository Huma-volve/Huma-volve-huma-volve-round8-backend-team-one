<?php

namespace Database\Factories;

use App\Models\SavedCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedCardFactory extends Factory
{
    protected $model = SavedCard::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'provider_token' => 'tok_'.$this->faker->regexify('[a-zA-Z0-9]{24}'),
            'brand' => $this->faker->creditCardType,
            'last_four' => $this->faker->numerify('####'),
            'exp_month' => $this->faker->numberBetween(1, 12),
            'exp_year' => $this->faker->numberBetween(date('Y') + 1, date('Y') + 5),
            'is_default' => $this->faker->boolean(20),
        ];
    }
}
