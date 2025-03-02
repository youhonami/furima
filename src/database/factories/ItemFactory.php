<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'img' => 'sample.jpg',
            'price' => $this->faker->numberBetween(1000, 10000),
            'condition_id' => Condition::inRandomOrder()->first()->id ?? 1,
            'user_id' => User::factory(),
        ];
    }
}
