<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\User;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'payment_method' => 'credit_card',
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'building' => $this->faker->word,
        ];
    }
}
