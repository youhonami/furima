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
            'user_id' => User::factory(),
            'postal_code' => '123-4567', // 固定値
            'address' => '東京都新宿区',
            'building' => 'テストマンション101'
        ];
    }
}
