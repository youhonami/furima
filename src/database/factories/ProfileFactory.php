<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;
use App\Models\User;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // ユーザーと関連付け
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress,
        ];
    }
}
