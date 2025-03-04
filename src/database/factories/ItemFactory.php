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
            'name' => $this->faker->word, // ✅ name カラムに値をセット
            'condition_id' => \App\Models\Condition::factory(),
            'img' => 'sample.jpg',
            'price' => 1000,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
